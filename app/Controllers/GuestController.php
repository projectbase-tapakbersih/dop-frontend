<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * Guest Controller
 * Handle guest order tracking without login
 * 
 * API Endpoints:
 * - GET /api/guest/orders/{order_number} - Get guest order detail
 */
class GuestController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form', 'format']);
    }

    /**
     * Track Order Page
     * GET /guest/track
     */
    public function trackPage()
    {
        $data = [
            'title' => 'Lacak Pesanan - Tapak Bersih',
            'order' => null,
            'error' => null,
            'searched' => false
        ];

        // Check if there's a search query
        $orderNumber = $this->request->getGet('order_number');
        
        if ($orderNumber) {
            $data['searched'] = true;
            $data['order_number'] = $orderNumber;
            
            // Try to get guest order
            $response = api_request("/guest/orders/{$orderNumber}", 'GET', [], false);
            
            log_message('info', "Guest Track Order {$orderNumber}: " . json_encode($response));
            
            if (isset($response['success']) && $response['success']) {
                $data['order'] = $response['data'] ?? null;
            } elseif (isset($response['order_number'])) {
                // Direct order object
                $data['order'] = $response;
            } else {
                $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan. Pastikan nomor pesanan benar.';
            }
        }

        return view('guest/track', $data);
    }

    /**
     * Track Order API (AJAX)
     * POST /guest/track
     */
    public function trackOrder()
    {
        $orderNumber = $this->request->getPost('order_number');
        
        if (empty($orderNumber)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor pesanan harus diisi'
            ]);
        }

        // Clean order number
        $orderNumber = trim(strtoupper($orderNumber));

        // API: GET /api/guest/orders/{order_number}
        $response = api_request("/guest/orders/{$orderNumber}", 'GET', [], false);
        
        log_message('info', "Guest Track Order API {$orderNumber}: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $response['data'] ?? null
            ]);
        } elseif (isset($response['order_number'])) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $response
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Pesanan tidak ditemukan'
        ]);
    }

    /**
     * Guest Order Detail Page
     * GET /guest/orders/{order_number}
     */
    public function orderDetail($orderNumber)
    {
        $data = [
            'title' => 'Detail Pesanan - ' . $orderNumber,
            'order' => null,
            'error' => null
        ];

        // API: GET /api/guest/orders/{order_number}
        $response = api_request("/guest/orders/{$orderNumber}", 'GET', [], false);
        
        log_message('info', "Guest Order Detail {$orderNumber}: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } elseif (isset($response['order_number'])) {
            $data['order'] = $response;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        return view('guest/order_detail', $data);
    }
}