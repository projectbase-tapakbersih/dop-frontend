<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Dashboard Controller
 * 
 * Fetches statistics from various API endpoints
 */
class DashboardController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form', 'format']);
    }

    /**
     * Dashboard Page
     */
    public function index()
    {
        if (!is_logged_in() || !is_admin()) {
            return redirect()->to('/auth/login')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Dashboard - Admin',
            'stats' => $this->getStats(),
            'recent_orders' => $this->getRecentOrders(),
            'chart_data' => $this->getChartData()
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Get Dashboard Statistics
     */
    private function getStats()
    {
        $stats = [
            'total_orders' => 0,
            'waiting_pickup' => 0,
            'in_process' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'total_revenue' => 0,
            'paid' => 0,
            'payment_pending' => 0,
            'payment_failed' => 0,
            'total_users' => 0,
            'total_services' => 0,
            'total_branches' => 0,
            'total_gallery' => 0,
            'total_promos' => 0
        ];

        // Try to get stats from admin stats endpoint
        $statsResponse = api_request('/admin/stats', 'GET', [], true);
        
        if (is_array($statsResponse)) {
            if (isset($statsResponse['success']) && $statsResponse['success']) {
                $statsData = $statsResponse['data'] ?? $statsResponse;
                $stats = array_merge($stats, $this->extractStats($statsData));
            } elseif (isset($statsResponse['total_orders'])) {
                $stats = array_merge($stats, $this->extractStats($statsResponse));
            }
        }

        // Fetch orders for order stats
        $ordersResponse = api_request('/admin/orders', 'GET', [], true);
        $orders = $this->parseResponse($ordersResponse);
        
        if (!empty($orders)) {
            $stats['total_orders'] = count($orders);
            $stats['total_revenue'] = 0;
            
            foreach ($orders as $order) {
                $orderStatus = $order['order_status'] ?? '';
                $paymentStatus = $order['payment_status'] ?? '';
                
                // Count by order status
                switch ($orderStatus) {
                    case 'waiting_pickup':
                        $stats['waiting_pickup']++;
                        break;
                    case 'in_process':
                    case 'processing':
                    case 'washing':
                    case 'drying':
                    case 'picked_up':
                    case 'on_the_way_to_workshop':
                    case 'arrived_at_workshop':
                        $stats['in_process']++;
                        break;
                    case 'completed':
                    case 'delivered':
                        $stats['completed']++;
                        break;
                    case 'cancelled':
                        $stats['cancelled']++;
                        break;
                }
                
                // Count by payment status
                switch ($paymentStatus) {
                    case 'paid':
                        $stats['paid']++;
                        $stats['total_revenue'] += floatval($order['total_amount'] ?? 0);
                        break;
                    case 'pending':
                        $stats['payment_pending']++;
                        break;
                    case 'failed':
                    case 'expired':
                        $stats['payment_failed']++;
                        break;
                }
            }
        }

        // Fetch users count
        $usersResponse = api_request('/admin/users', 'GET', [], true);
        $users = $this->parseResponse($usersResponse);
        $stats['total_users'] = count($users);

        // Fetch services count
        $servicesResponse = api_request('/services', 'GET', [], false);
        $services = $this->parseResponse($servicesResponse);
        $stats['total_services'] = count($services);

        // Fetch branches count
        $branchesResponse = api_request('/branches', 'GET', [], false);
        $branches = $this->parseResponse($branchesResponse);
        $stats['total_branches'] = count($branches);

        // Fetch gallery count
        $galleryResponse = api_request('/galleries', 'GET', [], true);
        $galleries = $this->parseResponse($galleryResponse);
        $stats['total_gallery'] = count($galleries);

        // Fetch promo codes count
        $promoResponse = api_request('/admin/promo-codes', 'GET', [], true);
        $promos = $this->parseResponse($promoResponse);
        $stats['total_promos'] = count($promos);

        log_message('info', 'Dashboard Stats: ' . json_encode($stats));

        return $stats;
    }

    /**
     * Get Recent Orders
     */
    private function getRecentOrders()
    {
        $response = api_request('/admin/orders', 'GET', ['limit' => 10], true);
        return $this->parseResponse($response);
    }

    /**
     * Get Chart Data
     */
    private function getChartData()
    {
        $chartData = [
            'daily' => [],
            'monthly' => [],
            'status' => []
        ];

        // Try to get chart data from API
        $revenueResponse = api_request('/admin/reports/revenue', 'GET', [], true);
        
        if (is_array($revenueResponse) && isset($revenueResponse['success']) && $revenueResponse['success']) {
            $data = $revenueResponse['data'] ?? [];
            $chartData['daily'] = $data['daily'] ?? [];
            $chartData['monthly'] = $data['monthly'] ?? [];
        }

        // Generate sample data if empty
        if (empty($chartData['daily'])) {
            $chartData['daily'] = $this->generateSampleDailyData();
        }

        if (empty($chartData['monthly'])) {
            $chartData['monthly'] = $this->generateSampleMonthlyData();
        }

        return $chartData;
    }

    /**
     * Generate sample daily data for chart
     */
    private function generateSampleDailyData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('d M', strtotime("-{$i} days"));
            $data[] = [
                'date' => $date,
                'label' => $date,
                'revenue' => 0,
                'value' => 0
            ];
        }
        return $data;
    }

    /**
     * Generate sample monthly data for chart
     */
    private function generateSampleMonthlyData()
    {
        $data = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $currentMonth = date('n') - 1;
        
        for ($i = 5; $i >= 0; $i--) {
            $monthIndex = ($currentMonth - $i + 12) % 12;
            $data[] = [
                'month' => $months[$monthIndex],
                'label' => $months[$monthIndex],
                'revenue' => 0,
                'value' => 0
            ];
        }
        return $data;
    }

    /**
     * Extract stats from response
     */
    private function extractStats($data)
    {
        $stats = [];
        $keys = [
            'total_orders', 'waiting_pickup', 'in_process', 'completed', 'cancelled',
            'total_revenue', 'paid', 'payment_pending', 'payment_failed',
            'total_users', 'total_services', 'total_branches', 'total_gallery', 'total_promos'
        ];
        
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $stats[$key] = $data[$key];
            }
        }
        
        return $stats;
    }

    /**
     * Parse API response
     */
    private function parseResponse($response)
    {
        $items = [];

        if (is_array($response)) {
            // Direct array of items
            if (isset($response[0]) && is_array($response[0])) {
                $items = $response;
            }
            // Success response with data
            elseif (isset($response['success']) && $response['success']) {
                $responseData = $response['data'] ?? [];
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $items = $responseData['data'];
                } elseif (is_array($responseData) && isset($responseData[0])) {
                    $items = $responseData;
                } elseif (is_array($responseData)) {
                    $items = $responseData;
                }
            }
            // Direct data without success field
            elseif (isset($response['data']) && is_array($response['data'])) {
                if (isset($response['data']['data'])) {
                    $items = $response['data']['data'];
                } else {
                    $items = $response['data'];
                }
            }
        }

        // Filter valid items (must have id or order_number)
        $validItems = [];
        foreach ($items as $item) {
            if (is_array($item) && (isset($item['id']) || isset($item['order_number']))) {
                $validItems[] = $item;
            }
        }

        return $validItems;
    }

    /**
     * Debug endpoint
     */
    public function debug()
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['error' => 'Access denied']);
        }

        $debug = [
            'stats' => $this->getStats(),
            'galleries_raw' => api_request('/galleries', 'GET', [], true),
            'promos_raw' => api_request('/admin/promo-codes', 'GET', [], true)
        ];

        return $this->response->setJSON($debug);
    }
}