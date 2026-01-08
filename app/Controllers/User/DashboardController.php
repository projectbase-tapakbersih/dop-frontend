<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Dashboard Controller
 */
class DashboardController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * Admin Dashboard
     */
    public function index()
    {
        // Debug logging
        log_message('info', '=== ADMIN DASHBOARD ACCESS ===');
        log_message('info', 'Session logged_in: ' . (session()->get('logged_in') ? 'true' : 'false'));
        log_message('info', 'Session auth_token: ' . (session()->get('auth_token') ? 'EXISTS' : 'NULL'));
        log_message('info', 'Session user: ' . json_encode(session()->get('user')));

        // Check if logged in
        if (!is_logged_in()) {
            log_message('warning', 'Admin access denied: Not logged in');
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if admin
        if (!is_admin()) {
            $user = session()->get('user');
            $role = $user['role'] ?? 'unknown';
            log_message('warning', "Admin access denied: User role is '{$role}', not 'admin'");
            return redirect()->to('/user/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman admin');
        }

        // Fetch dashboard data
        $data = [
            'title' => 'Dashboard Admin - Tapak Bersih',
            'user' => session()->get('user'),
            'stats' => $this->getDashboardStats(),
            'recent_orders' => $this->getRecentOrders(),
            'revenue_data' => $this->getRevenueData()
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
    {
        $stats = [
            'total_orders' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'total_revenue' => 0,
            'total_users' => 0,
            'total_services' => 0
        ];

        // Fetch orders
        $ordersResponse = api_request('/admin/orders', 'GET', [], true);
        if (isset($ordersResponse['success']) && $ordersResponse['success']) {
            $orders = $ordersResponse['data']['data'] ?? $ordersResponse['data'] ?? [];
            if (is_array($orders)) {
                $stats['total_orders'] = count($orders);
                foreach ($orders as $order) {
                    if (isset($order['order_status'])) {
                        if (in_array($order['order_status'], ['pending', 'waiting_pickup', 'in_process'])) {
                            $stats['pending_orders']++;
                        } elseif ($order['order_status'] === 'completed') {
                            $stats['completed_orders']++;
                        }
                    }
                    if (isset($order['total_amount'])) {
                        $stats['total_revenue'] += (float)$order['total_amount'];
                    }
                }
            }
        }

        // Fetch users
        $usersResponse = api_request('/admin/users', 'GET', [], true);
        if (isset($usersResponse['success']) && $usersResponse['success']) {
            $users = $usersResponse['data']['data'] ?? $usersResponse['data'] ?? [];
            $stats['total_users'] = is_array($users) ? count($users) : 0;
        }

        // Fetch services
        $servicesResponse = api_request('/services', 'GET', [], false);
        if (is_array($servicesResponse)) {
            $services = $servicesResponse['data'] ?? $servicesResponse;
            if (isset($services[0])) {
                $stats['total_services'] = count($services);
            }
        }

        return $stats;
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders(): array
    {
        $response = api_request('/admin/orders', 'GET', [], true);
        
        if (isset($response['success']) && $response['success']) {
            $orders = $response['data']['data'] ?? $response['data'] ?? [];
            return array_slice($orders, 0, 5);
        }

        return [];
    }

    /**
     * Get revenue data for chart
     */
    private function getRevenueData(): array
    {
        $months = [];
        $revenues = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = strtotime("-{$i} months");
            $months[] = date('M Y', $date);
            $revenues[] = rand(1000000, 10000000); // Placeholder
        }

        return [
            'labels' => $months,
            'data' => $revenues
        ];
    }

    /**
     * Debug endpoint - development only
     */
    public function debug()
    {
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON(['error' => 'Not available in production']);
        }

        return $this->response->setJSON([
            'session' => [
                'logged_in' => session()->get('logged_in'),
                'auth_token' => session()->get('auth_token') ? 'EXISTS (' . strlen(session()->get('auth_token')) . ' chars)' : 'NULL',
                'user' => session()->get('user'),
            ],
            'checks' => [
                'is_logged_in()' => is_logged_in(),
                'is_admin()' => is_admin(),
                'get_user_role()' => get_user_role(),
            ],
            'env' => [
                'API_BASE_URL' => env('API_BASE_URL'),
                'CI_ENVIRONMENT' => ENVIRONMENT,
            ]
        ]);
    }
}