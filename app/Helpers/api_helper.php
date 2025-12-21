<?php

if (!function_exists('api_request')) {
    /**
     * Universal API Request Helper
     * Updated untuk API Laravel Tapak Bersih
     * 
     * @param string $endpoint API endpoint (e.g., '/auth/login')
     * @param string $method HTTP method (GET, POST, PUT, DELETE, PATCH)
     * @param array $data Request payload
     * @param bool $auth Require authentication token (JWT Bearer)
     * @return array Response data
     */
    function api_request($endpoint, $method = 'GET', $data = [], $auth = false)
    {
        // Gunakan ngrok URL atau local
        $apiUrl = getenv('API_BASE_URL') ?: 'https://paradingly-yarest-sindy.ngrok-free.dev/api';
        $url = $apiUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'ngrok-skip-browser-warning: true' // Skip ngrok warning page
        ];

        // Add JWT Bearer token if required
        if ($auth) {
            $session = session();
            $token = $session->get('api_token');
            if ($token) {
                // Format: Authorization: Bearer {token}
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For ngrok & local dev

        // Set HTTP method and data
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

        // Handle connection errors
        if ($error) {
            log_message('error', 'API Request Error: ' . $error . ' | URL: ' . $url);
            return [
                'success' => false,
                'message' => 'Connection error: ' . $error,
                'http_code' => 0
            ];
        }

        // Parse JSON response
        $result = json_decode($response, true);
        
        // If JSON decode fails
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'JSON Decode Error: ' . json_last_error_msg() . ' | Response: ' . $response);
            return [
                'success' => false,
                'message' => 'Invalid response format',
                'http_code' => $httpCode,
                'raw_response' => $response
            ];
        }

        // Add HTTP code to result
        $result['http_code'] = $httpCode;

        // Normalize response structure
        if (!isset($result['success'])) {
            $result['success'] = $httpCode >= 200 && $httpCode < 300;
        }

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

if (!function_exists('is_guest')) {
    /**
     * Check if current session is guest
     */
    function is_guest()
    {
        $session = session();
        return $session->has('is_guest') && $session->get('is_guest') === true;
    }
}

if (!function_exists('get_user_data')) {
    /**
     * Get current logged in user data
     */
    function get_user_data()
    {
        $session = session();
        
        if (is_guest()) {
            return [
                'is_guest' => true,
                'name' => $session->get('guest_name'),
                'email' => $session->get('guest_email'),
                'phone' => $session->get('guest_phone')
            ];
        }
        
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

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Rupiah currency
     */
    function format_rupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format phone number (add +62 prefix if needed)
     */
    function format_phone($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return '+' . $phone;
    }
}