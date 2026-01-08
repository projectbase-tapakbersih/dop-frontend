<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin User Management Controller
 * 
 * API Endpoints:
 * - GET /api/admin/users - List all users
 * - POST /api/admin/users - Create user
 * - GET /api/admin/users/{id} - Show user
 * - PUT /api/admin/users/{id} - Update user
 * - DELETE /api/admin/users/{id} - Delete user
 */
class UserController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * List all users
     * API: GET /api/admin/users
     */
    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen User - Admin',
            'users' => [],
            'error' => null,
            'debug_info' => null
        ];

        $response = api_request('/admin/users', 'GET', [], true);
        
        log_message('info', 'Admin Users Response: ' . json_encode($response));

        if (ENVIRONMENT === 'development') {
            $data['debug_info'] = $response;
        }

        $users = $this->parseResponse($response);
        $data['users'] = $users;

        if (isset($response['message']) && empty($users)) {
            $data['error'] = $response['message'];
        }

        return view('admin/users/index', $data);
    }

    /**
     * Show user detail
     * API: GET /api/admin/users/{id}
     */
    public function show($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/users/{$id}", 'GET', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'data' => $response['data'] ?? $response]);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'User tidak ditemukan']);
    }

    /**
     * Store new user
     * API: POST /api/admin/users
     * 
     * Format:
     * {
     *   "name": "New User",
     *   "email": "newuser@example.com",
     *   "phone": "081234567892",
     *   "password": "secret123",
     *   "role": "admin"
     * }
     */
    public function store()
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $postData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role') ?? 'customer'
        ];

        log_message('info', 'User Store Data: ' . json_encode($postData));

        $response = api_request('/admin/users', 'POST', $postData, true);
        
        log_message('info', 'User Store Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'User berhasil ditambahkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal menambahkan user']);
    }

    /**
     * Update user
     * API: PUT /api/admin/users/{id}
     */
    public function update($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $postData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'role' => $this->request->getPost('role')
        ];

        // Only include password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $postData['password'] = $password;
        }

        log_message('info', 'User Update Data: ' . json_encode($postData));

        $response = api_request("/admin/users/{$id}", 'PUT', $postData, true);
        
        log_message('info', 'User Update Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'User berhasil diupdate']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal mengupdate user']);
    }

    /**
     * Delete user
     * API: DELETE /api/admin/users/{id}
     */
    public function delete($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/users/{$id}", 'DELETE', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'User berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal menghapus user']);
    }

    /**
     * Debug endpoint (development only)
     */
    public function debug()
    {
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON(['error' => 'Not available']);
        }

        $response = api_request('/admin/users', 'GET', [], true);
        return $this->response->setJSON($response);
    }

    /**
     * Parse API response to array
     */
    private function parseResponse($response)
    {
        $items = [];

        if (is_array($response)) {
            if (isset($response[0]) && is_array($response[0]) && isset($response[0]['id'])) {
                $items = $response;
            } elseif (isset($response['success']) && $response['success']) {
                $responseData = $response['data'] ?? [];
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $items = $responseData['data'];
                } elseif (is_array($responseData) && isset($responseData[0])) {
                    $items = $responseData;
                } elseif (is_array($responseData)) {
                    $items = $responseData;
                }
            } elseif (isset($response['data']) && is_array($response['data'])) {
                if (isset($response['data']['data'])) {
                    $items = $response['data']['data'];
                } else {
                    $items = $response['data'];
                }
            }
        }

        $validItems = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item['id'])) {
                $validItems[] = $item;
            }
        }

        return $validItems;
    }
}