<?php
namespace App\Controllers;

class Dashboard extends BaseController {
    public function index() {
        $user = session()->get('user');
        $data = [
            'user' => $user,
        ];
        return view('dashboard/index', $data);
    }
}