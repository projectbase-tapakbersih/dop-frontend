<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class OrderController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * List user's orders
     * GET /user/orders
     * API: GET /api/user/orders
     */
    public function myOrders()
    {
        if (!is_logged_in()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $data = [
            'title' => 'Pesanan Saya - Tapak Bersih',
            'orders' => [],
            'error' => null
        ];

        // API: GET /api/user/orders
        $response = api_request('/user/orders', 'GET', [], true);

        log_message('info', 'User Orders Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $responseData = $response['data'] ?? [];
            
            // Handle paginated response
            if (isset($responseData['data']) && is_array($responseData['data'])) {
                $data['orders'] = $responseData['data'];
            } else {
                $data['orders'] = is_array($responseData) ? $responseData : [];
            }
        }
        // Handle direct array response
        elseif (is_array($response) && isset($response[0])) {
            $data['orders'] = $response;
        }
        else {
            // Fallback: try to get from session stored orders
            $sessionOrders = session()->get('user_orders') ?? [];
            if (!empty($sessionOrders)) {
                $data['orders'] = $sessionOrders;
            } else {
                $data['error'] = $response['message'] ?? null;
            }
        }

        return view('user/orders/index', $data);
    }

    /**
     * Show order detail
     * GET /user/orders/{order_number}
     * API: GET /api/user/order/{orderNumber}
     */
    public function detail($orderNumber)
    {
        if (!is_logged_in()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $data = [
            'title' => 'Detail Pesanan - ' . $orderNumber,
            'order' => null,
            'error' => null
        ];

        // API: GET /api/user/order/{orderNumber}
        $response = api_request("/user/order/{$orderNumber}", 'GET', [], true);

        log_message('info', "User Order Detail {$orderNumber}: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } elseif (isset($response['order_number'])) {
            // Direct order object
            $data['order'] = $response;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        return view('user/orders/detail', $data);
    }

    /**
     * Cancel order
     * POST /user/orders/{order_number}/cancel
     * API: POST /api/user/order/{orderNumber}/cancel
     */
    public function cancelOrder($orderNumber)
    {
        if (!is_logged_in()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $reason = $this->request->getPost('reason') ?? 'Dibatalkan oleh customer';

        // API: POST /api/user/order/{orderNumber}/cancel
        $response = api_request("/user/order/{$orderNumber}/cancel", 'POST', [
            'reason' => $reason
        ], true);

        log_message('info', "Cancel User Order {$orderNumber}: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $response['message'] ?? 'Pesanan berhasil dibatalkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal membatalkan pesanan'
        ]);
    }

    /**
     * Track order (redirect to detail or show tracking)
     */
    public function track()
    {
        $orderNumber = $this->request->getGet('order_number');
        
        if ($orderNumber) {
            return redirect()->to("user/orders/{$orderNumber}");
        }
        
        return view('user/orders/track', [
            'title' => 'Lacak Pesanan - Tapak Bersih'
        ]);
    }
}