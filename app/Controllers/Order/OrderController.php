<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Order Management Controller
 * 
 * API Endpoints:
 * - GET /api/admin/orders - List all orders
 * - GET /api/admin/orders/{order_number} - Show order detail
 * - PATCH /api/admin/orders/{order_number}/status - Update status
 * - DELETE /api/admin/orders/{order_number} - Cancel/Delete order
 */
class OrderController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form', 'format']);
    }

    /**
     * List all orders
     */
    public function index()
    {
        if (!is_logged_in() || !is_admin()) {
            return redirect()->to('/auth/login')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen Order - Admin',
            'orders' => [],
            'error' => null
        ];

        $response = api_request('/admin/orders', 'GET', [], true);
        
        log_message('info', 'Admin Orders Response: ' . json_encode($response));

        $orders = $this->parseResponse($response);
        $data['orders'] = $orders;

        if (isset($response['message']) && empty($orders)) {
            $data['error'] = $response['message'];
        }

        return view('admin/orders/index', $data);
    }

    /**
     * Show order detail
     */
    public function show($orderNumber)
    {
        if (!is_logged_in() || !is_admin()) {
            return redirect()->to('/auth/login')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Detail Order - Admin',
            'order' => null,
            'error' => null
        ];

        $response = api_request("/admin/orders/{$orderNumber}", 'GET', [], true);
        
        log_message('info', 'Admin Order Detail Response: ' . json_encode($response));

        if (is_array($response)) {
            if (isset($response['success']) && $response['success']) {
                $data['order'] = $response['data'] ?? null;
            } elseif (isset($response['order_number'])) {
                $data['order'] = $response;
            } elseif (isset($response['data'])) {
                $data['order'] = $response['data'];
            } elseif (isset($response['message'])) {
                $data['error'] = $response['message'];
            }
        }

        if (!$data['order']) {
            $data['error'] = 'Order tidak ditemukan';
        }

        return view('admin/orders/detail', $data);
    }

    /**
     * Update order status
     * API: PATCH /api/admin/orders/{order_number}/status
     * 
     * Format yang diharapkan API:
     * {
     *   "order_status": "in_process",
     *   "notes": "Catatan opsional"
     * }
     */
    public function updateStatus($orderNumber)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Get status from POST - support both 'status' and 'order_status' field names
        $status = $this->request->getPost('order_status') ?? $this->request->getPost('status');
        $notes = $this->request->getPost('notes') ?? '';

        if (empty($status)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Status harus dipilih'
            ]);
        }

        // Send dengan field name yang benar: order_status
        $postData = [
            'order_status' => $status
        ];

        // Tambahkan notes jika ada
        if (!empty($notes)) {
            $postData['notes'] = $notes;
        }

        log_message('info', 'Order Status Update - Order: ' . $orderNumber);
        log_message('info', 'Order Status Update - Data: ' . json_encode($postData));

        $response = api_request("/admin/orders/{$orderNumber}/status", 'PATCH', $postData, true);
        
        log_message('info', 'Order Status Update Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Status order berhasil diupdate'
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => $response['message'] ?? 'Gagal mengupdate status order'
        ]);
    }

    /**
     * Cancel/Delete order
     * API: DELETE /api/admin/orders/{order_number}
     */
    public function cancel($orderNumber)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/orders/{$orderNumber}", 'DELETE', [], true);
        
        log_message('info', 'Order Cancel Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Order berhasil dibatalkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => $response['message'] ?? 'Gagal membatalkan order'
        ]);
    }

    /**
     * Parse API response to array
     */
    private function parseResponse($response)
    {
        $items = [];

        if (is_array($response)) {
            if (isset($response[0]) && is_array($response[0]) && isset($response[0]['order_number'])) {
                $items = $response;
            } elseif (isset($response['success']) && $response['success']) {
                $responseData = $response['data'] ?? [];
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $items = $responseData['data'];
                } elseif (is_array($responseData) && isset($responseData[0])) {
                    $items = $responseData;
                } elseif (is_array($responseData)) {
                    $items = $responseData;
                }
            } elseif (isset($response['data']) && is_array($response['data'])) {
                if (isset($response['data']['data'])) {
                    $items = $response['data']['data'];
                } else {
                    $items = $response['data'];
                }
            }
        }

        $validItems = [];
        foreach ($items as $item) {
            if (is_array($item) && (isset($item['order_number']) || isset($item['id']))) {
                $validItems[] = $item;
            }
        }

        return $validItems;
    }
}