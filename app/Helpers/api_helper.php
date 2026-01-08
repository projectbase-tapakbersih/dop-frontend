<?php

/**
 * API Helper Functions - Complete Version
 * With all user/auth functions
 */

if (!function_exists('api_request')) {
    /**
     * Make API request to Laravel backend
     */
    function api_request(string $endpoint, string $method = 'GET', array $data = [], bool $requireAuth = false): array
    {
        $baseUrl = env('API_BASE_URL', 'http://localhost:8000/api');
        $baseUrl = trim($baseUrl, "'\"");
        
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'ngrok-skip-browser-warning: true',
        ];

        if ($requireAuth) {
            $token = session()->get('auth_token');
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        $ch = curl_init();

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
        ]);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
        }

        log_message('debug', "API Request: {$method} {$url}");

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($errno) {
            log_message('error', "CURL Error [{$errno}]: {$error}");
            return ['success' => false, 'message' => 'Gagal terhubung ke server: ' . $error];
        }

        if (empty($response)) {
            return ['success' => false, 'message' => 'Server tidak memberikan response'];
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            if (strpos($response, '<html') !== false) {
                return ['success' => false, 'message' => 'API server returned HTML instead of JSON'];
            }
            return ['success' => false, 'message' => 'Invalid JSON response'];
        }

        if (!isset($result['success'])) {
            $result['success'] = ($httpCode >= 200 && $httpCode < 300);
        }

        if ($httpCode >= 400) {
            $result['success'] = false;
        }

        return $result;
    }
}

if (!function_exists('api_upload')) {
    /**
     * Upload file to API
     */
    function api_upload(string $endpoint, array $files = [], array $data = [], bool $requireAuth = false): array
    {
        $baseUrl = env('API_BASE_URL', 'http://localhost:8000/api');
        $baseUrl = trim($baseUrl, "'\"");
        
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $headers = [
            'Accept: application/json',
            'ngrok-skip-browser-warning: true',
        ];

        if ($requireAuth) {
            $token = session()->get('auth_token');
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        $postData = $data;

        foreach ($files as $fieldName => $filePath) {
            if (file_exists($filePath)) {
                $postData[$fieldName] = new CURLFile($filePath);
            }
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'message' => 'Upload failed: ' . $error];
        }

        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'message' => 'Invalid response'];
        }

        return $result;
    }
}

// ============================================
// USER/AUTH HELPER FUNCTIONS
// ============================================

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in(): bool
    {
        $loggedIn = session()->get('logged_in');
        $token = session()->get('auth_token');
        
        return $loggedIn === true && !empty($token);
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin
     */
    function is_admin(): bool
    {
        if (!is_logged_in()) {
            return false;
        }

        $user = session()->get('user');
        
        if (!is_array($user)) {
            return false;
        }

        $role = $user['role'] ?? null;
        
        return $role === 'admin';
    }
}

if (!function_exists('current_user')) {
    /**
     * Get current logged in user
     */
    function current_user(): ?array
    {
        if (!is_logged_in()) {
            return null;
        }
        return session()->get('user');
    }
}

if (!function_exists('get_user_data')) {
    /**
     * Get current user data (alias for current_user)
     * Used in layouts/main.php
     */
    function get_user_data(): ?array
    {
        return current_user();
    }
}

if (!function_exists('auth_token')) {
    /**
     * Get current auth token
     */
    function auth_token(): ?string
    {
        return session()->get('auth_token');
    }
}

if (!function_exists('get_user_role')) {
    /**
     * Get current user's role
     */
    function get_user_role(): ?string
    {
        $user = current_user();
        return $user['role'] ?? null;
    }
}

if (!function_exists('get_user_name')) {
    /**
     * Get current user's name
     */
    function get_user_name(): string
    {
        $user = current_user();
        return $user['name'] ?? 'User';
    }
}

if (!function_exists('get_user_email')) {
    /**
     * Get current user's email
     */
    function get_user_email(): ?string
    {
        $user = current_user();
        return $user['email'] ?? null;
    }
}

if (!function_exists('get_user_id')) {
    /**
     * Get current user's ID
     */
    function get_user_id(): ?int
    {
        $user = current_user();
        return isset($user['id']) ? (int)$user['id'] : null;
    }
}