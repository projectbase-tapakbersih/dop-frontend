<?php

namespace App\Controllers\Order;

use App\Controllers\BaseController;

class GuestOrderController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * Show guest order detail
     * GET /guest/order/{order_number}
     * API: GET /api/guest/orders/{order_number}
     */
    public function detail($orderNumber)
    {
        $data = [
            'title' => 'Detail Pesanan - ' . $orderNumber,
            'order' => null,
            'error' => null
        ];

        // API: GET /api/guest/orders/{order_number}
        $response = api_request("/guest/orders/{$orderNumber}", 'GET');

        log_message('info', "Guest Order Detail {$orderNumber}: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } elseif (isset($response['order_number'])) {
            // Direct order object
            $data['order'] = $response;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan. Pastikan nomor pesanan benar.';
        }

        return view('guest/order_detail', $data);
    }

    /**
     * Cancel guest order
     * POST /guest/order/{order_number}/cancel
     * API: POST /api/guest/orders/{order_number}/cancel
     */
    public function cancel($orderNumber)
    {
        $reason = $this->request->getPost('reason') ?? 'Dibatalkan oleh customer';

        // API: POST /api/guest/orders/{order_number}/cancel
        $response = api_request("/guest/orders/{$orderNumber}/cancel", 'POST', [
            'reason' => $reason
        ]);

        log_message('info', "Cancel Guest Order {$orderNumber}: " . json_encode($response));

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
     * Track guest order page
     * GET /guest/track
     */
    public function track()
    {
        $orderNumber = $this->request->getGet('order_number');
        
        if ($orderNumber) {
            return redirect()->to("guest/order/{$orderNumber}");
        }
        
        return view('guest/track', [
            'title' => 'Lacak Pesanan - Tapak Bersih'
        ]);
    }
}