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
        if (is_logged_in()) {
            return redirect()->to(is_admin() ? '/admin/dashboard' : '/');
        }

        return view('auth/login');
    }

    /**
     * Process Login
     * API: POST /api/auth/login
     */
    public function processLogin()
    {
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
            'password' => $this->request->getPost('password'),
            'remember_me' => $this->request->getPost('remember') ? true : false
        ];

        $response = api_request('/auth/login', 'POST', $data);

        log_message('info', 'Login Response: ' . json_encode($response));

        // Handle API request errors
        if (!is_array($response)) {
            log_message('error', 'API returned non-array response');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ]);
        }

        // Laravel API returns: { "message", "user", "token" } for success
        if (isset($response['token']) && isset($response['user'])) {
            $userData = $response['user'];
            $token = $response['token'];

            // Validate required user fields
            if (!isset($userData['id']) || !isset($userData['email'])) {
                log_message('error', 'Invalid user data structure: ' . json_encode($response));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Response dari server tidak valid. Silakan hubungi administrator.'
                ]);
            }

            // Login API returns role, default to customer if not present
            $role = $userData['role'] ?? 'customer';

            // Set session
            session()->set([
                'user_id' => $userData['id'],
                'user_name' => $userData['name'],
                'user_email' => $userData['email'],
                'user_phone' => $userData['phone'] ?? null,
                'user_role' => $role,
                'api_token' => $token,
                'is_logged_in' => true,
                'is_guest' => false
            ]);

            $redirectUrl = $role === 'admin' 
                ? base_url('admin/dashboard') 
                : base_url('/');

            return $this->response->setJSON([
                'success' => true,
                'message' => $response['message'] ?? 'Login berhasil!',
                'redirect' => $redirectUrl
            ]);
        }

        // Login failed
        log_message('error', 'Login failed - Response: ' . json_encode($response));
        
        // Laravel validation errors come directly in response
        $errors = [];
        $errorMessage = 'Login gagal. Periksa email dan password Anda.';
        
        // Check for validation errors from Laravel
        if (isset($response['email']) && is_array($response['email'])) {
            $errors['email'] = $response['email'];
            $errorMessage = $response['email'][0];
        }
        
        if (isset($response['password']) && is_array($response['password'])) {
            $errors['password'] = $response['password'];
            if (empty($errorMessage) || $errorMessage === 'Login gagal. Periksa email dan password Anda.') {
                $errorMessage = $response['password'][0];
            }
        }
        
        // Fallback to response message if provided
        if (isset($response['message'])) {
            $errorMessage = $response['message'];
        } elseif (isset($response['error'])) {
            $errorMessage = $response['error'];
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $errorMessage,
            'errors' => $errors
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
     * API: POST /api/auth/register
     * Note: Register API does NOT return 'role' in user object, always default to 'customer'
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

        $response = api_request('/auth/register', 'POST', $data);

        log_message('info', 'Register Response: ' . json_encode($response));

        if (!is_array($response)) {
            log_message('error', 'Register API returned non-array response');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ]);
        }

        // Laravel Register returns: { "message", "user": { name, email, phone, id }, "token" }
        // Note: NO 'role' field in register response
        if (isset($response['token']) && isset($response['user'])) {
            $userData = $response['user'];
            $token = $response['token'];

            // Validate minimum required fields
            if (!isset($userData['id'])) {
                log_message('error', 'Register response missing user ID: ' . json_encode($response));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Response dari server tidak valid. Silakan hubungi administrator.'
                ]);
            }

            // Register API does NOT return role, always set as 'customer'
            $role = 'customer';

            // Set session
            session()->set([
                'user_id' => $userData['id'],
                'user_name' => $userData['name'] ?? '',
                'user_email' => $userData['email'] ?? '',
                'user_phone' => $userData['phone'] ?? null,
                'user_role' => $role,
                'api_token' => $token,
                'is_logged_in' => true,
                'is_guest' => false
            ]);

            log_message('info', 'Registration successful for user ID: ' . $userData['id']);

            return $this->response->setJSON([
                'success' => true,
                'message' => $response['message'] ?? 'Registrasi berhasil! Selamat datang.',
                'redirect' => base_url('/')
            ]);
        }

        // Registration failed - no token or user in response
        log_message('error', 'Registration failed - Missing token or user in response: ' . json_encode($response));
        
        // Laravel validation errors come directly in response (not nested in 'errors')
        // Example: { "email": ["The email has already been taken."], "http_code": 422 }
        $errors = [];
        $errorMessage = 'Registrasi gagal';
        
        // Check for validation errors from Laravel
        if (isset($response['email']) && is_array($response['email'])) {
            $errors['email'] = $response['email'];
            $errorMessage = $response['email'][0]; // Use first error as main message
        }
        
        if (isset($response['phone']) && is_array($response['phone'])) {
            $errors['phone'] = $response['phone'];
            if (empty($errorMessage) || $errorMessage === 'Registrasi gagal') {
                $errorMessage = $response['phone'][0];
            }
        }
        
        if (isset($response['name']) && is_array($response['name'])) {
            $errors['name'] = $response['name'];
            if (empty($errorMessage) || $errorMessage === 'Registrasi gagal') {
                $errorMessage = $response['name'][0];
            }
        }
        
        if (isset($response['password']) && is_array($response['password'])) {
            $errors['password'] = $response['password'];
            if (empty($errorMessage) || $errorMessage === 'Registrasi gagal') {
                $errorMessage = $response['password'][0];
            }
        }
        
        // Fallback to response message if provided
        if (isset($response['message']) && (empty($errors) || $errorMessage === 'Registrasi gagal')) {
            $errorMessage = $response['message'];
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $errorMessage,
            'errors' => $errors
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

        session()->set([
            'is_guest' => true,
            'is_logged_in' => false,
            'guest_name' => $this->request->getPost('guest_name'),
            'guest_phone' => $this->request->getPost('guest_phone'),
            'guest_email' => $this->request->getPost('guest_email')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data tamu berhasil disimpan',
            'redirect' => base_url('order/create')
        ]);
    }

    /**
     * Logout
     * API: POST /api/auth/logout
     */
    public function logout()
    {
        if (is_logged_in() && !is_guest()) {
            $response = api_request('/auth/logout', 'POST', [], true);
            log_message('info', 'Logout Response: ' . json_encode($response));
        }

        session()->destroy();

        return redirect()->to('/')->with('success', 'Anda berhasil logout');
    }

    /**
     * Send OTP Page
     */
    public function sendOtpPage()
    {
        return view('auth/send_otp');
    }

    /**
     * Send OTP
     * API: POST /api/auth/send-otp
     */
    public function sendOtp()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'email' => $this->request->getPost('email')
        ];

        $response = api_request('/auth/send-otp', 'POST', $data);

        return $this->response->setJSON($response);
    }

    /**
     * Verify OTP Page
     */
    public function verifyOtpPage()
    {
        $email = $this->request->getGet('email');
        return view('auth/verify_otp', ['email' => $email]);
    }

    /**
     * Verify OTP
     * API: POST /api/auth/verify-otp
     */
    public function verifyOtp()
    {
        $rules = [
            'email' => 'required|valid_email',
            'otp' => 'required|exact_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'otp' => $this->request->getPost('otp')
        ];

        $response = api_request('/auth/verify-otp', 'POST', $data);

        if (isset($response['token']) && isset($response['user'])) {
            $userData = $response['user'];
            $token = $response['token'];
            
            // Verify OTP might not return role either, default to customer
            $role = $userData['role'] ?? 'customer';

            session()->set([
                'user_id' => $userData['id'],
                'user_name' => $userData['name'] ?? '',
                'user_email' => $userData['email'] ?? '',
                'user_phone' => $userData['phone'] ?? null,
                'user_role' => $role,
                'api_token' => $token,
                'is_logged_in' => true
            ]);
        }

        return $this->response->setJSON($response);
    }

    /**
     * Request Reset Password Page
     */
    public function forgotPasswordPage()
    {
        return view('auth/forgot_password');
    }

    /**
     * Request Reset Password
     * API: POST /api/auth/request-reset-password
     */
    public function requestResetPassword()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'email' => $this->request->getPost('email')
        ];

        $response = api_request('/auth/request-reset-password', 'POST', $data);

        return $this->response->setJSON($response);
    }

    /**
     * Reset Password Page
     */
    public function resetPasswordPage()
    {
        $token = $this->request->getGet('token');
        $email = $this->request->getGet('email');
        
        return view('auth/reset_password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset Password
     * API: POST /api/auth/reset-password
     */
    public function resetPassword()
    {
        $rules = [
            'email' => 'required|valid_email',
            'token' => 'required',
            'password' => 'required|min_length[6]',
            'password_confirmation' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'token' => $this->request->getPost('token'),
            'password' => $this->request->getPost('password'),
            'password_confirmation' => $this->request->getPost('password_confirmation')
        ];

        $response = api_request('/auth/reset-password', 'POST', $data);

        return $this->response->setJSON($response);
    }
}