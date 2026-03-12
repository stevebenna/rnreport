<?php

namespace App\Controllers;

use App\Models\SongModel;

class Reports extends BaseController {
    protected SongModel $songs;

    public function __construct() {
        $this->songs = new SongModel();
    }

    protected function token(): ?string {
        return session()->get('access_token');
    }

    public function index() {
        return view('reports/scf');
    }

    public function process() {
        $file = $this->request->getFile('scf_zip');

        if (! $file || ! $file->isValid()) {
            session()->setFlashdata('error', 'Nessun file caricato o file non valido.');
            return redirect()->to('/report-scf');
        }

        $extension = strtolower($file->getClientExtension() ?? '');
        if ($extension !== 'zip') {
            session()->setFlashdata('error', 'Carica un file ZIP valido.');
            return redirect()->to('/report-scf');
        }

        $tmpDir = WRITEPATH . 'uploads/scf_' . bin2hex(random_bytes(8));
        if (! mkdir($tmpDir, 0755, true) && ! is_dir($tmpDir)) {
            session()->setFlashdata('error', 'Impossibile creare la cartella temporanea.');
            return redirect()->to('/report-scf');
        }

        try {
            $zipPath = $file->getTempName();
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new \RuntimeException('Impossibile aprire il file ZIP.');
            }

            $zip->extractTo($tmpDir);
            $zip->close();

            $csvFiles = glob($tmpDir . '/*.csv');
            $allRows = [];

            foreach ($csvFiles as $csvFile) {
                $allRows = array_merge($allRows, $this->parseCsvFile($csvFile));
            }

            // Keep only one entry per timestamp (first encountered)
            $byTimestamp = [];
            foreach ($allRows as $row) {
                if (! isset($byTimestamp[$row['timestamp']])) {
                    $byTimestamp[$row['timestamp']] = $row;
                }
            }

            ksort($byTimestamp);
            $rows = array_values($byTimestamp);

            $outputRows = [];
            $itsrRows = [];
            $token = $this->token();

            $songMap = $this->buildSongMap($token);

            foreach ($rows as $i => $row) {
                $key = $this->normalizeTrackKey($row['artist'], $row['song']);
                if (! isset($songMap[$key])) {
                    continue;
                }

                $song = $songMap[$key];

                $date = $row['date'];
                $time = $row['time'];

                // Adjust 24:xx:yy to 00:xx:yy and shift date forward
                if (strpos($time, '24:') === 0) {
                    [$h, $m, $s] = explode(':', $time);
                    $time = sprintf('00:%02d:%02d', (int) $m, (int) $s);
                    $date = date('Y-m-d', strtotime($date . ' +1 day'));
                }

                $duration = '';
                $seconds = '';

                if (isset($rows[$i + 1])) {
                    $next = $rows[$i + 1];
                    $diff = $next['timestamp'] - $row['timestamp'];

                    if ($diff > 59) {
                        $hours = floor($diff / 3600);
                        $minutes = floor(($diff % 3600) / 60);
                        $seconds = $diff % 60;
                        $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                        $outputRows[] = [
                            'artist' => $row['artist'],
                            'song' => $row['song'],
                            'date' => date('d/m/Y', strtotime($date)),
                            'time' => $time,
                            'duration' => $duration,
                            'seconds' => $diff,
                        ];
                    }
                }

                $itsrRows[] = [
                    'date_emissione' => date('d/m/Y', strtotime($date)),
                    'orario_emissione' => $time,
                    'titolo' => $row['song'],
                    'artista_principale' => $row['artist'],
                    'durata' => $duration,
                    'data_pubblicazione' => '',
                    'versione' => '',
                    'isrc' => $song['isrc'] ?? '',
                    'etichetta' => $song['label'] ?? '',
                    'produttore' => '',
                    'album' => '',
                ];
            }

            // Create out.csv output file
            $outPath = $tmpDir . '/out.csv';
            $outHandle = fopen($outPath, 'w');
            if ($outHandle === false) {
                throw new \RuntimeException('Impossibile creare il file di output.');
            }

            fputcsv($outHandle, ['Artista', 'Canzone', 'Data', 'Ora', 'Durata', 'Durata (secondi)']);
            foreach ($outputRows as $out) {
                fputcsv($outHandle, [
                    $out['artist'],
                    $out['song'],
                    $out['date'],
                    $out['time'],
                    $out['duration'],
                    $out['seconds'],
                ]);
            }
            fclose($outHandle);

            // Create itsr.csv output file
            $itsrPath = $tmpDir . '/itsr.csv';
            $itsrHandle = fopen($itsrPath, 'w');
            if ($itsrHandle === false) {
                throw new \RuntimeException('Impossibile creare il file itsr.');
            }

            fputcsv($itsrHandle, [
                'Data Emissione*',
                'Orario Emissione*',
                'Titolo*',
                'Artista principale*',
                'Durata^',
                'Data di pubblicazione^',
                'Versione',
                'ISRC^',
                'Etichetta*',
                'Produttore',
                'Album',
            ]);

            foreach ($itsrRows as $out) {
                fputcsv($itsrHandle, [
                    $out['date_emissione'],
                    $out['orario_emissione'],
                    $out['titolo'],
                    $out['artista_principale'],
                    $out['durata'],
                    $out['data_pubblicazione'],
                    $out['versione'],
                    $out['isrc'],
                    $out['etichetta'],
                    $out['produttore'],
                    $out['album'],
                ]);
            }
            fclose($itsrHandle);

            // Build zip for immediate download
            $zipPath = $tmpDir . '/scf_report_' . bin2hex(random_bytes(6)) . '.zip';
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
                throw new \RuntimeException('Impossibile creare il file ZIP.');
            }

            $zip->addFile($outPath, 'csf.csv');
            $zip->addFile($itsrPath, 'itsright.csv');
            $zip->close();

            $zipContents = file_get_contents($zipPath);
            if ($zipContents === false) {
                throw new \RuntimeException('Impossibile leggere il file ZIP.');
            }

            $response = service('response');
            $response = $response->setHeader('Content-Type', 'application/zip');
            $response = $response->setHeader('Content-Disposition', 'attachment; filename="scf_report_'.date('Ymd_His').'.zip"');
            $response->setBody($zipContents);

            return $response;
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Errore durante l’elaborazione: ' . $e->getMessage());
            return redirect()->to('/report-scf');
        } finally {
            // Clean up all temporary files
            $this->cleanupTempDir($tmpDir, []);
        }
    }

    private function normalizeTrackKey(string $artist, string $song): string {
        $string = strtolower(trim($artist) . '|' . trim($song));
        return preg_replace('/[^a-z0-9]/', '', $string);
    }

    private function buildSongMap(string $token): array {
        $songs = $this->songs->all($token);
        $map = [];
        foreach ($songs as $song) {
            if (! isset($song['artist'], $song['song'])) {
                continue;
            }
            $key = $this->normalizeTrackKey($song['artist'], $song['song']);
            if ($key === '') {
                continue;
            }
            $map[$key] = $song;
        }
        return $map;
    }

    private function parseCsvFile(string $path): array {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        $rows = [];
        $header = null;

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if ($header === null) {
                $header = array_map(static fn($h) => strtolower(trim(trim($h, ' "'))), $data);
                continue;
            }

            if (count($data) < 3) {
                continue;
            }

            $record = array_combine($header, $data);
            if (! is_array($record)) {
                continue;
            }

            $artist = $this->getCsvColumn($record, ['artist', 'artista']);
            $song = $this->getCsvColumn($record, ['track', 'song', 'canzone']);
            $playedAt = $this->getCsvColumn($record, ['played at', 'played_at', 'playedat']);

            if ($artist === null || $song === null || $playedAt === null) {
                continue;
            }

            $artist = trim($artist);
            $song = trim($song);
            $playedAt = trim($playedAt);

            if ($artist === '' || $song === '' || stripos($artist, 'Spot -') === 0 || strtoupper($artist) === 'N/A' || strtoupper($song) === 'N/A') {
                continue;
            }

            // Remove timezone offset if present
            if (strpos($playedAt, '+') !== false) {
                $playedAt = explode('+', $playedAt)[0];
            }

            // Normalize whitespace
            $playedAt = trim($playedAt);

            $dateTime = \DateTime::createFromFormat('d M Y - H:i:s', $playedAt);
            if (! $dateTime) {
                // try with other patterns
                $dateTime = \DateTime::createFromFormat('d M Y - H:i', $playedAt);
            }

            if (! $dateTime) {
                // try generic parse
                try {
                    $dateTime = new \DateTime($playedAt);
                } catch (\Exception $e) {
                    continue;
                }
            }

            $rows[] = [
                'artist' => $artist,
                'song' => $song,
                'played_at' => $playedAt,
                'timestamp' => $dateTime->getTimestamp(),
                'date' => $dateTime->format('Y-m-d'),
                'time' => $dateTime->format('H:i:s'),
            ];
        }

        fclose($handle);
        return $rows;
    }

    private function getCsvColumn(array $row, array $candidates): ?string {
        foreach ($candidates as $col) {
            if (array_key_exists($col, $row) && strlen(trim((string) $row[$col])) > 0) {
                return (string) $row[$col];
            }
        }
        return null;
    }

    /**
     * Remove temporary files and directories.
     *
     * @param string $dir
     * @param string[] $keepPrefixes Files starting with these prefixes will be kept.
     */
    private function cleanupTempDir(string $dir, array $keepPrefixes = []): void {
        if (! is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $path = $fileinfo->getPathname();
            $base = basename($path);
            foreach ($keepPrefixes as $prefix) {
                if (str_starts_with($base, $prefix)) {
                    continue 2;
                }
            }

            if ($fileinfo->isDir()) {
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
