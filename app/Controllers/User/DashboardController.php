<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

/**
 * User Dashboard Controller
 * 
 * API Endpoints:
 * - GET /api/user/orders - Get user's orders
 */
class DashboardController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form', 'format']);
    }

    /**
     * User Dashboard
     * GET /user/dashboard
     */
    public function index()
    {
        // Check if logged in
        if (!is_logged_in()) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $data = [
            'title' => 'Dashboard - Tapak Bersih',
            'user' => get_user_data(),
            'stats' => $this->getUserStats(),
            'orders' => $this->getRecentOrders()
        ];

        return view('user/dashboard', $data);
    }

    /**
     * Get user statistics
     */
    private function getUserStats(): array
    {
        $stats = [
            'total_orders' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'total_spent' => 0
        ];

        // Fetch user orders - GET /api/user/orders
        $response = api_request('/user/orders', 'GET', [], true);
        
        log_message('info', 'User Dashboard Orders Response: ' . json_encode($response));

        $orders = [];
        
        if (isset($response['success']) && $response['success']) {
            $responseData = $response['data'] ?? [];
            if (isset($responseData['data']) && is_array($responseData['data'])) {
                $orders = $responseData['data'];
            } elseif (is_array($responseData)) {
                $orders = $responseData;
            }
        } elseif (is_array($response) && isset($response[0])) {
            $orders = $response;
        }

        if (!empty($orders)) {
            $stats['total_orders'] = count($orders);
            
            foreach ($orders as $order) {
                $orderStatus = $order['order_status'] ?? '';
                $paymentStatus = $order['payment_status'] ?? '';
                
                // Pending orders (not completed or cancelled)
                if (in_array($orderStatus, ['waiting_pickup', 'dalam_penjemputan', 'in_progress', 'ready_for_delivery', 'on_delivery'])) {
                    $stats['pending_orders']++;
                }
                
                // Completed orders
                if ($orderStatus === 'completed') {
                    $stats['completed_orders']++;
                }
                
                // Total spent (only paid orders)
                if ($paymentStatus === 'paid') {
                    $stats['total_spent'] += floatval($order['total_amount'] ?? 0);
                }
            }
        }

        return $stats;
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders(): array
    {
        $response = api_request('/user/orders', 'GET', [], true);
        
        $orders = [];
        
        if (isset($response['success']) && $response['success']) {
            $responseData = $response['data'] ?? [];
            if (isset($responseData['data']) && is_array($responseData['data'])) {
                $orders = $responseData['data'];
            } elseif (is_array($responseData)) {
                $orders = $responseData;
            }
        } elseif (is_array($response) && isset($response[0])) {
            $orders = $response;
        }

        // Return only the 5 most recent
        return array_slice($orders, 0, 5);
    }
}