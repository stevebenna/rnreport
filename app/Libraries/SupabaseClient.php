<?php
namespace App\Libraries;

use GuzzleHttp\Client;

class SupabaseClient {
    protected $client;
    protected $url;
    protected $key;

    public function __construct() {
        $this->url = env('SUPABASE_URL');
        $this->key = env('SUPABASE_ANON_KEY');
        $this->client = new Client([
            'base_uri' => $this->url,
            'headers'  => [
                'apikey'        => $this->key,
                'Content-Type'  => 'application/json',
            ]
        ]);
    }

    public function login(string $email, string $password) {
        $response = $this->client->post('/auth/v1/token?grant_type=password', [
            'json' => [
                'email'    => $email,
                'password' => $password,
            ]
        ]);
        return json_decode($response->getBody(), true);
    }

    public function logout(string $accessToken) {
        $response = $this->client->post('/auth/v1/logout', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);
        return $response->getStatusCode() === 204;
    }
}