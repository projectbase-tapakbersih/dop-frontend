<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class OrderController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Show my orders
     * Note: API might not have specific endpoint for user orders
     * May need to filter on backend or use query params
     */
    public function myOrders()
    {
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        $data = [
            'title' => 'Pesanan Saya',
            'orders' => [],
            'error' => null
        ];

        // Try to get user orders
        // This might need adjustment based on actual API
        $response = api_request('/orders', 'GET', [], true);

        if (isset($response['success']) && $response['success']) {
            $data['orders'] = $response['data'] ?? [];
        } else {
            $data['error'] = $response['message'] ?? 'Gagal memuat pesanan';
        }

        return view('user/orders', $data);
    }

    /**
     * Show order detail
     * API: GET /api/orders/{order_number}
     */
    public function detail($orderNumber)
    {
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        $data = [
            'title' => 'Detail Pesanan - ' . $orderNumber,
            'order' => null,
            'error' => null
        ];

        $response = api_request("/orders/{$orderNumber}", 'GET', [], true);

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        return view('user/order_detail', $data);
    }
}
