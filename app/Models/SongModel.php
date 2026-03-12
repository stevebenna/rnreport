<?php

namespace App\Models;

use App\Libraries\SupabaseClient;

class SongModel {
    protected SupabaseClient $supabase;
    protected string $table = 'songs';

    public function __construct() {
        $this->supabase = new SupabaseClient();
    }

    public function paginate(?string $search, int $page, int $perPage, string $token): array {
        // Apply optional search on artist/song with ilike.
        $params = [
            'order'  => 'artist.asc',
            'limit'  => $perPage,
            'offset' => ($page - 1) * $perPage,
        ];

        if (! empty($search)) {
            $clean = str_replace([',', '*'], ['', ''], $search);
            $like = '*' . $clean . '*';
            $params['or'] = "artist.ilike.$like,song.ilike.$like";
        }

        return $this->supabase->getTableWithCount($this->table, '*', $token, $params);
    }

    public function find(string $id, string $token): ?array {
        $rows = $this->supabase->getTable($this->table, '*', $token, [
            'id' => "eq.$id",
            'limit' => 1,
        ]);

        return is_array($rows) && count($rows) ? $rows[0] : null;
    }

    public function create(array $data, string $token): array {
        $rows = $this->supabase->insert($this->table, $data, $token);
        return is_array($rows) && count($rows) ? $rows[0] : [];
    }

    public function update(string $id, array $data, string $token): array {
        $rows = $this->supabase->update($this->table, $data, "id=eq.$id", $token);
        return is_array($rows) && count($rows) ? $rows[0] : [];
    }

    public function delete(string $id, string $token): bool {
        return $this->supabase->delete($this->table, "id=eq.$id", $token);
    }

    public static function containers(): array {
        return [
            'Lezioni di poesia',
            'The Blessed Hellride',
            'Love you live',
            'Prog/notte',
        ];
    }
}
