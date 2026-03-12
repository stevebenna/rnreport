<?php

namespace App\Libraries;

use GuzzleHttp\Exception\GuzzleException;

class SupabaseAuth {
    protected SupabaseClient $client;

    public function __construct() {
        $this->client = new SupabaseClient();
    }

    /**
     * Sign in with email and password.
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws \Exception When sign in fails.
     */
    public function signIn(string $email, string $password): array {
        try {
            return $this->client->login($email, $password);
        } catch (GuzzleException $e) {
            $message = $e->getMessage();

            if (method_exists($e, 'getResponse') && $e->getResponse() !== null) {
                try {
                    $body = (string) $e->getResponse()->getBody();
                    $data = json_decode($body, true);
                    if (is_array($data) && isset($data['error'])) {
                        $message = $data['error'];
                    }
                } catch (\Throwable $_) {
                    // Ignore and fall back to the original message
                }
            }

            throw new \Exception('Login failed: ' . $message);
        }
    }
}
