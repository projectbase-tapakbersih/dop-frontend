<?php

if (!function_exists('api_request')) {
    /**
     * Universal API Request Helper
     * 
     * @param string $endpoint API endpoint (e.g., '/auth/login')
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param array $data Request payload
     * @param bool $auth Require authentication token
     * @return array Response data
     */
    function api_request($endpoint, $method = 'GET', $data = [], $auth = false)
    {
        $apiUrl = getenv('API_BASE_URL') ?: 'http://localhost:8000/api';
        $url = $apiUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        // Add authorization token if required
        if ($auth) {
            $session = session();
            $token = $session->get('api_token');
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $error,
                'http_code' => 0
            ];
        }

        $result = json_decode($response, true);
        $result['http_code'] = $httpCode;

        return $result;
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in()
    {
        $session = session();
        return $session->has('user_id') && $session->has('api_token');
    }
}

if (!function_exists('get_user_data')) {
    /**
     * Get current logged in user data
     */
    function get_user_data()
    {
        $session = session();
        return [
            'user_id' => $session->get('user_id'),
            'name' => $session->get('user_name'),
            'email' => $session->get('user_email'),
            'role' => $session->get('user_role'),
            'phone' => $session->get('user_phone')
        ];
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin
     */
    function is_admin()
    {
        $session = session();
        return $session->get('user_role') === 'admin';
    }
}