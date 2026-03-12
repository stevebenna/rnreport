<?php
namespace App\Controllers;

use App\Libraries\SupabaseClient;

class AuthController extends BaseController {
    protected $supabase;

    public function __construct() {
        $this->supabase = new SupabaseClient();
    }

    public function login() {
        if ($this->request->getMethod() === 'POST') {
            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            try {
                $result = $this->supabase->login($email, $password);
                if (isset($result['access_token'])) {
                    // Store token and user in session
                    session()->set([
                        'user'         => $result['user'],
                        'access_token' => $result['access_token'],
                        'logged_in'    => true,
                    ]);
                    return redirect()->to('/dashboard');
                }

                return redirect()->back()->with('error', 'Login failed');

            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        return view('auth/login');
    }

    public function logout() {
        $token = session()->get('access_token');
        if ($token) {
            $this->supabase->logout($token);
        }
        session()->destroy();
        return redirect()->to('/');
    }
}