<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url', 'api']);
    }

    /**
     * Show Login Page
     */
    public function login()
    {
        // Redirect if already logged in
        if (is_logged_in()) {
            return redirect()->to(is_admin() ? '/admin/dashboard' : '/');
        }

        return view('auth/login');
    }

    /**
     * Process Login
     */
    public function processLogin()
    {
        // Validation rules
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        // Call Laravel API
        $response = api_request('/auth/login', 'POST', $data);

        if (isset($response['success']) && $response['success']) {
            // Store user data in session
            $userData = $response['data']['user'];
            $token = $response['data']['token'];

            session()->set([
                'user_id' => $userData['id'],
                'user_name' => $userData['name'],
                'user_email' => $userData['email'],
                'user_phone' => $userData['phone'] ?? null,
                'user_role' => $userData['role'],
                'api_token' => $token,
                'is_logged_in' => true
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $userData['role'] === 'admin' ? base_url('admin/dashboard') : base_url('/')
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Login failed'
        ]);
    }

    /**
     * Show Register Page
     */
    public function register()
    {
        if (is_logged_in()) {
            return redirect()->to('/');
        }

        return view('auth/register');
    }

    /**
     * Process Registration
     */
    public function processRegister()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'password' => 'required|min_length[6]',
            'password_confirmation' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password'),
            'password_confirmation' => $this->request->getPost('password_confirmation')
        ];

        // Call Laravel API
        $response = api_request('/auth/register', 'POST', $data);

        if (isset($response['success']) && $response['success']) {
            // Auto login after registration
            $userData = $response['data']['user'];
            $token = $response['data']['token'];

            session()->set([
                'user_id' => $userData['id'],
                'user_name' => $userData['name'],
                'user_email' => $userData['email'],
                'user_phone' => $userData['phone'],
                'user_role' => $userData['role'],
                'api_token' => $token,
                'is_logged_in' => true
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registration successful',
                'redirect' => base_url('/')
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Registration failed',
            'errors' => $response['errors'] ?? []
        ]);
    }

    /**
     * Guest Checkout - Store guest data in session
     */
    public function guestCheckout()
    {
        $rules = [
            'guest_name' => 'required|min_length[3]',
            'guest_phone' => 'required|numeric|min_length[10]',
            'guest_email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Store guest data in session
        session()->set([
            'is_guest' => true,
            'guest_name' => $this->request->getPost('guest_name'),
            'guest_phone' => $this->request->getPost('guest_phone'),
            'guest_email' => $this->request->getPost('guest_email')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Guest data saved',
            'redirect' => base_url('order/create')
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Call API logout if logged in
        if (is_logged_in()) {
            api_request('/auth/logout', 'POST', [], true);
        }

        // Destroy session
        session()->destroy();

        return redirect()->to('/')->with('message', 'Logout successful');
    }
}