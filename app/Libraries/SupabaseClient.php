<?php
namespace App\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

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

    protected function authHeaders(?string $token = null): array {
        $headers = [
            'apikey' => $this->key,
            'Content-Type' => 'application/json',
        ];

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

    public function request(string $method, string $path, array $options = [], ?string $token = null) {
        $options['headers'] = array_merge($this->authHeaders($token), $options['headers'] ?? []);
        $response = $this->client->request($method, $path, $options);
        $body = (string) $response->getBody();
        return $body === '' ? [] : json_decode($body, true);
    }

    public function requestWithResponse(string $method, string $path, array $options = [], ?string $token = null): ResponseInterface {
        $options['headers'] = array_merge($this->authHeaders($token), $options['headers'] ?? []);
        return $this->client->request($method, $path, $options);
    }

    public function getTable(string $table, string $select = '*', ?string $token = null, array $params = []): array {
        $params = array_merge(['select' => $select], $params);
        $path = '/rest/v1/' . $table . '?' . http_build_query($params);
        return $this->request('GET', $path, [], $token);
    }

    public function getTableWithCount(string $table, string $select = '*', ?string $token = null, array $params = []): array {
        $params = array_merge(['select' => $select], $params);
        $path = '/rest/v1/' . $table . '?' . http_build_query($params);

        $response = $this->requestWithResponse('GET', $path, [
            'headers' => ['Prefer' => 'count=exact'],
        ], $token);
        
        $body = (string) $response->getBody();
        $data = $body === '' ? [] : json_decode($body, true);
        $count = null;

        $contentRange = $response->getHeaderLine('Content-Range');
        if (preg_match('/\d+-\d+\/(\d+)/', $contentRange, $matches)) {
            $count = (int) $matches[1];
        }

        return [
            'data' => $data,
            'count' => $count,
        ];
    }

    public function insert(string $table, array $data, ?string $token = null): array {
        $path = '/rest/v1/' . $table;
        return $this->request('POST', $path, [
            'headers' => ['Prefer' => 'return=representation'],
            'json' => $data,
        ], $token);
    }

    public function update(string $table, array $data, string $match, ?string $token = null): array {
        $path = '/rest/v1/' . $table . '?' . $match;
        return $this->request('PATCH', $path, [
            'headers' => ['Prefer' => 'return=representation'],
            'json' => $data,
        ], $token);
    }

    public function delete(string $table, string $match, ?string $token = null): bool {
        $path = '/rest/v1/' . $table . '?' . $match;
        $response = $this->client->request('DELETE', $path, [
            'headers' => $this->authHeaders($token),
        ]);
        return $response->getStatusCode() === 204;
    }
}
