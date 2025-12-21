<?php

namespace App\Controllers\Order;

use App\Controllers\BaseController;

class OrderController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Show order detail for authenticated users
     * API: GET /api/orders/{order_number}
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

        $response = api_request("/orders/{$orderNumber}", 'GET', [], true);

        log_message('info', "Order {$orderNumber} Response: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        return view('order/detail', $data);
    }

    /**
     * Track order (public with order number)
     * API: GET /api/orders/{order_number}
     */
    public function track($orderNumber)
    {
        $data = [
            'title' => 'Lacak Pesanan - ' . $orderNumber,
            'order' => null,
            'error' => null
        ];

        $response = api_request("/orders/{$orderNumber}", 'GET');

        log_message('info', "Track Order {$orderNumber} Response: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        return view('order/track', $data);
    }

    /**
     * Update order
     * API: PUT /api/orders/{order_number}
     */
    public function update($orderNumber)
    {
        if (!is_logged_in()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = [
            'pickup_address' => $this->request->getPost('pickup_address'),
            'pickup_date' => $this->request->getPost('pickup_date'),
            'pickup_time' => $this->request->getPost('pickup_time'),
            'shoe_type' => $this->request->getPost('shoe_type'),
            'special_notes' => $this->request->getPost('special_notes')
        ];

        // Remove null values
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        $response = api_request("/orders/{$orderNumber}", 'PUT', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Cancel order
     * API: DELETE /api/orders/{order_number}
     */
    public function cancel($orderNumber)
    {
        if (!is_logged_in()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = [
            'reason' => $this->request->getPost('reason')
        ];

        $response = api_request("/orders/{$orderNumber}", 'DELETE', $data, true);

        return $this->response->setJSON($response);
    }
}