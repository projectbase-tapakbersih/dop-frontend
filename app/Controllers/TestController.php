<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TestController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Test API Connection with ngrok
     * Access: http://localhost:8080/test/api
     */
    public function testApi()
    {
        $tests = [];

        // Test 1: Get Services
        $tests['services'] = api_request('/services', 'GET');

        // Test 2: Get Service Detail
        $tests['service_detail'] = api_request('/services/1', 'GET');

        // Test 3: Get Branches
        $tests['branches'] = api_request('/branches', 'GET');

        // Test 4: Get Active Branches
        $tests['active_branches'] = api_request('/branches/active', 'GET');

        // Test 5: Get Branch Detail
        $tests['branch_detail'] = api_request('/branches/1', 'GET');

        // Test 6: Get Galleries
        $tests['galleries'] = api_request('/galleries', 'GET');

        // Test 7: Get Service Gallery
        $tests['service_gallery'] = api_request('/services/1/gallery', 'GET');

        // Test 8: Get Gallery Detail
        $tests['gallery_detail'] = api_request('/gallery/1', 'GET');

        // Display results
        echo '<html><head><title>API Connection Test</title>';
        echo '<style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
            h1 { color: #667eea; }
            .test-section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            pre { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px; overflow-x: auto; }
            .success { color: #28a745; }
            .error { color: #dc3545; }
            .info { color: #17a2b8; }
        </style></head><body>';
        
        echo '<h1>🧪 API Connection Test - Tapak Bersih</h1>';
        echo '<p class="info"><strong>API Base URL:</strong> ' . getenv('API_BASE_URL') . '</p>';
        echo '<p class="info"><strong>Date:</strong> ' . date('Y-m-d H:i:s') . '</p>';
        echo '<hr>';

        foreach ($tests as $name => $result) {
            $success = isset($result['success']) && $result['success'];
            $statusClass = $success ? 'success' : 'error';
            
            echo '<div class="test-section">';
            echo '<h3>' . ucwords(str_replace('_', ' ', $name)) . '</h3>';
            echo '<p class="' . $statusClass . '">';
            echo '<strong>Status:</strong> ' . ($success ? '✅ SUCCESS' : '❌ FAILED') . '<br>';
            echo '<strong>HTTP Code:</strong> ' . ($result['http_code'] ?? 'N/A');
            echo '</p>';
            echo '<pre>' . json_encode($result, JSON_PRETTY_PRINT) . '</pre>';
            echo '</div>';
        }
        
        echo '</body></html>';
    }

    /**
     * Test Login with Real Credentials
     */
    public function testLogin()
    {
        return view('test/login_test');
    }

    /**
     * Process Test Login
     */
    public function processTestLogin()
    {
        $data = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        $response = api_request('/auth/login', 'POST', $data);

        return $this->response->setJSON($response);
    }

    /**
     * Display Session Data
     */
    public function sessionInfo()
    {
        echo '<html><head><title>Session Info</title>';
        echo '<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}pre{background:#1e1e1e;color:#d4d4d4;padding:15px;border-radius:5px;}</style>';
        echo '</head><body>';
        
        echo '<h1>Session Information</h1>';
        echo '<pre>';
        print_r(session()->get());
        echo '</pre>';
        
        echo '<hr>';
        echo '<h2>Helper Functions</h2>';
        echo '<p>is_logged_in(): ' . (is_logged_in() ? '<span style="color:green">✅ true</span>' : '<span style="color:red">❌ false</span>') . '</p>';
        echo '<p>is_guest(): ' . (is_guest() ? '<span style="color:green">✅ true</span>' : '<span style="color:red">❌ false</span>') . '</p>';
        echo '<p>is_admin(): ' . (is_admin() ? '<span style="color:green">✅ true</span>' : '<span style="color:red">❌ false</span>') . '</p>';
        
        if (is_logged_in() || is_guest()) {
            echo '<h3>User Data:</h3>';
            echo '<pre>';
            print_r(get_user_data());
            echo '</pre>';
        }
        
        echo '<hr>';
        echo '<a href="' . base_url('test/api') . '" style="padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;">Test API</a> ';
        echo '<a href="' . base_url('auth/logout') . '" style="padding:10px 20px;background:#dc3545;color:white;text-decoration:none;border-radius:5px;">Logout</a>';
        
        echo '</body></html>';
    }
}