<?php

namespace App\Controllers;

use App\Models\SongModel;

class Songs extends BaseController {
    protected SongModel $songs;

    public function __construct() {
        $this->songs = new SongModel();
    }

    protected function token(): ?string {
        return session()->get('access_token');
    }

    public function index() {
        $search = trim((string) $this->request->getGet('q'));
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = 10;

        $songs = [];
        $count = 0;

        try {
            $result = $this->songs->paginate($search, $page, $perPage, $this->token());
            $songs = $result['data'] ?? [];
            $count = $result['count'] ?? 0;
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Impossibile caricare le canzoni: ' . $e->getMessage());
        }

        return view('songs/index', [
            'songs' => $songs,
            'count' => $count,
            'page' => $page,
            'perPage' => $perPage,
            'search' => $search,
        ]);
    }

    public function show($id) {
        $song = $this->songs->find($id, $this->token());
        if (! $song) {
            session()->setFlashdata('error', 'Canzone non trovata.');
            return redirect()->to('/canzoni');
        }

        return view('songs/show', [
            'song' => $song,
        ]);
    }

    public function create() {
        return view('songs/form', [
            'containers' => SongModel::containers(),
            'song' => [],
            'errors' => [],
        ]);
    }

    public function store() {
        $data = $this->collectSongData();

        if (! $this->validate($this->rules())) {
            return view('songs/form', [
                'containers' => SongModel::containers(),
                'song' => $data,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        try {
            $this->songs->create($data, $this->token());
            session()->setFlashdata('success', 'Canzone creata con successo.');
            return redirect()->to('/canzoni');
        } catch (\Exception $e) {
            return view('songs/form', [
                'containers' => SongModel::containers(),
                'song' => $data,
                'errors' => ['supabase' => $e->getMessage()],
            ]);
        }
    }

    public function edit($id) {
        $song = $this->songs->find($id, $this->token());
        if (! $song) {
            session()->setFlashdata('error', 'Canzone non trovata.');
            return redirect()->to('/canzoni');
        }

        return view('songs/form', [
            'containers' => SongModel::containers(),
            'song' => $song,
            'errors' => [],
        ]);
    }

    public function update($id) {
        $data = $this->collectSongData();

        if (! $this->validate($this->rules())) {
            $data['id'] = $id;
            return view('songs/form', [
                'containers' => SongModel::containers(),
                'song' => $data,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        try {
            $this->songs->update($id, $data, $this->token());
            session()->setFlashdata('success', 'Canzone aggiornata con successo.');
            return redirect()->to('/canzoni');
        } catch (\Exception $e) {
            $data['id'] = $id;
            return view('songs/form', [
                'containers' => SongModel::containers(),
                'song' => $data,
                'errors' => ['supabase' => $e->getMessage()],
            ]);
        }
    }

    public function delete($id) {
        try {
            $this->songs->delete($id, $this->token());
            session()->setFlashdata('success', 'Canzone eliminata.');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Impossibile eliminare la canzone: ' . $e->getMessage());
        }
        return redirect()->to('/canzoni');
    }

    protected function collectSongData(): array {
        return [
            'artist' => $this->request->getPost('artist'),
            'song'   => $this->request->getPost('song'),
            'author' => $this->request->getPost('author'),
            'label'  => $this->request->getPost('label'),
            'iswc'   => $this->request->getPost('iswc'),
            'isrc'   => $this->request->getPost('isrc'),
            'duration' => $this->request->getPost('duration'),
            'level'    => $this->request->getPost('level'),
            'container' => $this->request->getPost('container'),
            'is_easy_listening' => $this->request->getPost('is_easy_listening') ? true : false,
        ];
    }

    protected function rules(): array {
        return [
            'artist' => 'required',
            'song' => 'required',
            'author' => 'required',
            'label' => 'required',
            'duration' => 'required|integer',
            'level' => 'permit_empty|integer|in_list[1,2,3,4,5]',
            'container' => 'permit_empty',
            'is_easy_listening' => 'permit_empty',
        ];
    }
}
