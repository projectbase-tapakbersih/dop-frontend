<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

/**
 * Auth Controller - Fixed Version
 * With better API response handling and debugging
 */
class AuthController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form', 'cookie']);
    }

    /**
     * Show login page
     */
    public function loginPage()
    {
        // Redirect if already logged in
        if (session()->get('logged_in')) {
            $user = session()->get('user');
            if (isset($user['role']) && $user['role'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            }
            return redirect()->to('/user/dashboard');
        }

        $data = [
            'title' => 'Login - Tapak Bersih',
            'error' => session()->getFlashdata('error'),
            'success' => session()->getFlashdata('success')
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $rememberMe = $this->request->getPost('remember_me') ? true : false;

        if (empty($email) || empty($password)) {
            return redirect()->back()->with('error', 'Email dan password harus diisi');
        }

        $postData = [
            'email' => $email,
            'password' => $password,
            'remember_me' => $rememberMe
        ];

        log_message('info', '=== LOGIN ATTEMPT ===');
        log_message('info', 'Email: ' . $email);

        $response = api_request('/auth/login', 'POST', $postData, false);

        log_message('info', 'Login API Response: ' . json_encode($response));

        // Handle different response formats
        $token = null;
        $user = null;
        $success = false;

        if (is_array($response)) {
            // Check for success
            $success = isset($response['success']) ? $response['success'] : false;
            
            // If no explicit success field, check for token presence
            if (!$success && (isset($response['token']) || isset($response['access_token']))) {
                $success = true;
            }

            if ($success) {
                // Extract token - try multiple possible keys
                $token = $response['token'] 
                    ?? $response['access_token'] 
                    ?? $response['data']['token'] 
                    ?? $response['data']['access_token'] 
                    ?? null;

                // Extract user - try multiple possible structures
                $user = $response['user'] 
                    ?? $response['data']['user'] 
                    ?? $response['data'] 
                    ?? null;

                // If user is nested deeper
                if (is_array($user) && isset($user['user'])) {
                    $user = $user['user'];
                }

                log_message('info', 'Token extracted: ' . ($token ? 'YES' : 'NO'));
                log_message('info', 'User extracted: ' . json_encode($user));
            }
        }

        if ($success && $token) {
            // Store in session
            session()->set('auth_token', $token);
            session()->set('logged_in', true);

            if ($user) {
                // Ensure user has role field
                if (!isset($user['role'])) {
                    $user['role'] = 'customer'; // default role
                }
                session()->set('user', $user);
                
                log_message('info', 'User role: ' . $user['role']);
                log_message('info', 'Session user set: ' . json_encode(session()->get('user')));
            }

            // Redirect based on role
            $role = $user['role'] ?? 'customer';
            
            if ($role === 'admin') {
                log_message('info', 'Redirecting to admin dashboard');
                return redirect()->to('/admin/dashboard')->with('success', 'Selamat datang, Admin!');
            }
            
            log_message('info', 'Redirecting to user dashboard');
            return redirect()->to('/user/dashboard')->with('success', 'Login berhasil!');
        }

        // Login failed
        $errorMessage = $response['message'] ?? 'Login gagal. Periksa email dan password Anda.';
        log_message('error', 'Login failed: ' . $errorMessage);
        
        return redirect()->back()->with('error', $errorMessage)->withInput();
    }

    /**
     * Show register page
     */
    public function registerPage()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Register - Tapak Bersih',
            'error' => session()->getFlashdata('error'),
            'success' => session()->getFlashdata('success')
        ];

        return view('auth/register', $data);
    }

    /**
     * Process registration
     */
    public function register()
    {
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $password = $this->request->getPost('password');
        $passwordConfirmation = $this->request->getPost('password_confirmation');

        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            return redirect()->back()->with('error', 'Semua field harus diisi')->withInput();
        }

        if ($password !== $passwordConfirmation) {
            return redirect()->back()->with('error', 'Password dan konfirmasi password tidak cocok')->withInput();
        }

        $postData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation
        ];

        log_message('info', '=== REGISTER ATTEMPT ===');
        
        $response = api_request('/auth/register', 'POST', $postData, false);
        
        log_message('info', 'Register Response: ' . json_encode($response));

        $success = isset($response['success']) ? $response['success'] : false;
        
        if (!$success && (isset($response['token']) || isset($response['access_token']) || isset($response['user']))) {
            $success = true;
        }

        if ($success) {
            $token = $response['token'] ?? $response['access_token'] ?? $response['data']['token'] ?? null;
            $user = $response['user'] ?? $response['data']['user'] ?? $response['data'] ?? null;

            if ($token && $user) {
                if (!isset($user['role'])) {
                    $user['role'] = 'customer';
                }
                
                session()->set('auth_token', $token);
                session()->set('user', $user);
                session()->set('logged_in', true);
                
                return redirect()->to('/user/dashboard')->with('success', 'Registrasi berhasil!');
            }

            return redirect()->to('/auth/login')->with('success', 'Registrasi berhasil! Silakan login.');
        }

        $errorMessage = $response['message'] ?? 'Registrasi gagal.';
        return redirect()->back()->with('error', $errorMessage)->withInput();
    }

    /**
     * Logout
     */
    public function logout()
    {
        $token = session()->get('auth_token');

        if ($token) {
            api_request('/auth/logout', 'POST', [], true);
        }

        session()->remove(['auth_token', 'user', 'logged_in']);
        session()->destroy();

        return redirect()->to('/auth/login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Debug session - for development only
     */
    public function debugSession()
    {
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON(['error' => 'Not available']);
        }

        return $this->response->setJSON([
            'logged_in' => session()->get('logged_in'),
            'auth_token' => session()->get('auth_token') ? 'EXISTS' : 'NULL',
            'user' => session()->get('user'),
            'session_id' => session_id()
        ]);
    }

    // Password reset methods
    public function forgotPasswordPage()
    {
        return view('auth/forgot_password', ['title' => 'Lupa Password']);
    }

    public function requestResetPassword()
    {
        $email = $this->request->getPost('email');
        
        if (empty($email)) {
            return redirect()->back()->with('error', 'Email harus diisi');
        }

        $response = api_request('/auth/request-reset-password', 'POST', ['email' => $email], false);

        if (isset($response['success']) && $response['success']) {
            return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda');
        }

        return redirect()->back()->with('error', $response['message'] ?? 'Gagal mengirim link reset password');
    }

    public function resetPasswordPage()
    {
        $data = [
            'title' => 'Reset Password',
            'token' => $this->request->getGet('token'),
            'email' => $this->request->getGet('email')
        ];
        return view('auth/reset_password', $data);
    }

    public function resetPassword()
    {
        $postData = [
            'email' => $this->request->getPost('email'),
            'token' => $this->request->getPost('token'),
            'password' => $this->request->getPost('password'),
            'password_confirmation' => $this->request->getPost('password_confirmation')
        ];

        $response = api_request('/auth/reset-password', 'POST', $postData, false);

        if (isset($response['success']) && $response['success']) {
            return redirect()->to('/auth/login')->with('success', 'Password berhasil direset. Silakan login.');
        }

        return redirect()->back()->with('error', $response['message'] ?? 'Gagal mereset password')->withInput();
    }

    public function sendOtp()
    {
        $email = $this->request->getPost('email');
        $response = api_request('/auth/send-otp', 'POST', ['email' => $email], false);
        return $this->response->setJSON($response);
    }

    public function verifyOtp()
    {
        $postData = [
            'email' => $this->request->getPost('email'),
            'otp' => $this->request->getPost('otp')
        ];
        $response = api_request('/auth/verify-otp', 'POST', $postData, false);
        return $this->response->setJSON($response);
    }
}