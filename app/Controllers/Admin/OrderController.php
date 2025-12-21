<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class OrderController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * List all orders (admin)
     * Note: API route not explicitly listed, might need to use /api/orders
     */
    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen Pesanan',
            'orders' => [],
            'error' => null
        ];

        // Try to get orders (might need specific admin endpoint)
        $response = api_request('/orders', 'GET', [], true);

        if (isset($response['success']) && $response['success']) {
            $data['orders'] = $response['data'] ?? [];
        }

        return view('admin/orders/index', $data);
    }

    /**
     * Show order detail
     * API: GET /api/admin/orders/{order_number}
     */
    public function detail($orderNumber)
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Detail Pesanan - ' . $orderNumber,
            'order' => null,
            'error' => null
        ];

        $response = api_request("/admin/orders/{$orderNumber}", 'GET', [], true);

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        return view('admin/orders/detail', $data);
    }

    /**
     * Update order status
     * API: PATCH /api/admin/orders/{order_number}/status
     */
    public function updateStatus($orderNumber)
    {
        if (!is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = [
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        $response = api_request("/admin/orders/{$orderNumber}/status", 'PATCH', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Cancel order (admin)
     * API: DELETE /api/admin/orders/{order_number}
     */
    public function cancel($orderNumber)
    {
        if (!is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = [
            'reason' => $this->request->getPost('reason')
        ];

        $response = api_request("/admin/orders/{$orderNumber}", 'DELETE', $data, true);

        return $this->response->setJSON($response);
    }
}