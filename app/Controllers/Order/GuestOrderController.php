<?php

namespace App\Controllers\Order;

use App\Controllers\BaseController;

class GuestOrderController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Show guest order detail
     * API: GET /api/guest/order/{order_number}
     */
    public function detail($orderNumber)
    {
        $data = [
            'title' => 'Detail Pesanan - ' . $orderNumber,
            'order' => null,
            'error' => null
        ];

        $response = api_request("/guest/order/{$orderNumber}", 'GET');

        log_message('info', "Guest Order {$orderNumber} Response: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        return view('order/guest_detail', $data);
    }
}