<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * List users
     * API: GET /api/users
     */
    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen User',
            'users' => []
        ];

        $response = api_request('/users', 'GET', [], true);

        if (isset($response['success']) && $response['success']) {
            $data['users'] = $response['data'] ?? [];
        }

        return view('admin/users/index', $data);
    }

    /**
     * Show user
     * API: GET /api/users/{id}
     */
    public function show($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $response = api_request("/users/{$id}", 'GET', [], true);

        return $this->response->setJSON($response);
    }

    /**
     * Create user
     * API: POST /api/users
     */
    public function create()
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role')
        ];

        $response = api_request('/users', 'POST', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Update user
     * API: PUT /api/users/{id}
     */
    public function update($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = array_filter([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'role' => $this->request->getPost('role')
        ]);

        $response = api_request("/users/{$id}", 'PUT', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Delete user
     * API: DELETE /api/users/{id}
     */
    public function delete($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $response = api_request("/users/{$id}", 'DELETE', [], true);

        return $this->response->setJSON($response);
    }
}