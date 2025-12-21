<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class ProfileController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * Show user profile
     */
    public function index()
    {
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        $data = [
            'title' => 'Profile Saya',
            'user' => get_user_data()
        ];

        return view('user/profile', $data);
    }

    /**
     * Update profile
     * Note: Might need to use PUT /api/users/{id}
     */
    public function update()
    {
        if (!is_logged_in()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $userId = session()->get('user_id');
        
        $data = array_filter([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone')
        ]);

        $response = api_request("/users/{$userId}", 'PUT', $data, true);

        // Update session if successful
        if (isset($response['success']) && $response['success']) {
            $userData = $response['data'] ?? [];
            if (!empty($userData)) {
                session()->set([
                    'user_name' => $userData['name'] ?? session()->get('user_name'),
                    'user_email' => $userData['email'] ?? session()->get('user_email'),
                    'user_phone' => $userData['phone'] ?? session()->get('user_phone')
                ]);
            }
        }

        return $this->response->setJSON($response);
    }
}