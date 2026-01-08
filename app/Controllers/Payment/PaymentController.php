<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;

class PaymentController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * Show payment page
     * GET /payment/{order_number}
     */
    public function show($orderNumber)
    {
        $data = [
            'title' => 'Pembayaran - ' . $orderNumber,
            'order' => null,
            'payment' => null,
            'error' => null
        ];

        // Get order detail first
        // Try user order, then guest order
        $response = null;
        
        if (is_logged_in()) {
            $response = api_request("/user/order/{$orderNumber}", 'GET', [], true);
        }
        
        if (!isset($response['success']) || !$response['success']) {
            $response = api_request("/guest/orders/{$orderNumber}", 'GET');
        }

        log_message('info', "Payment Show {$orderNumber}: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } elseif (isset($response['order_number'])) {
            $data['order'] = $response;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        return view('payment/show', $data);
    }

    /**
     * Process payment (create payment)
     * POST /payment/{order_number}/confirm
     * API: POST /api/payments/{order_number}
     */
    public function confirm($orderNumber)
    {
        $paymentMethod = $this->request->getPost('payment_method');

        // API: POST /api/payments/{order_number}
        $response = api_request("/payments/{$orderNumber}", 'POST', [
            'payment_method' => $paymentMethod
        ], is_logged_in());

        log_message('info', "Payment Confirm {$orderNumber}: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $paymentData = $response['data'] ?? [];
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $response['message'] ?? 'Pembayaran berhasil diproses',
                'data' => $paymentData,
                'redirect' => $paymentData['payment_url'] ?? $paymentData['redirect_url'] ?? null
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal memproses pembayaran'
        ]);
    }

    /**
     * Check payment status
     * GET /payment/{order_number}/status
     * API: GET /api/payments/{order_number}/status
     */
    public function status($orderNumber)
    {
        // API: GET /api/payments/{order_number}/status
        $response = api_request("/payments/{$orderNumber}/status", 'GET', [], is_logged_in());

        log_message('info', "Payment Status {$orderNumber}: " . json_encode($response));

        return $this->response->setJSON($response);
    }

    /**
     * Cancel payment
     * POST /payment/{order_number}/cancel
     * API: POST /api/payments/{order_number}/cancel
     */
    public function cancel($orderNumber)
    {
        // API: POST /api/payments/{order_number}/cancel
        $response = api_request("/payments/{$orderNumber}/cancel", 'POST', [], is_logged_in());

        log_message('info', "Payment Cancel {$orderNumber}: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $response['message'] ?? 'Pembayaran berhasil dibatalkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal membatalkan pembayaran'
        ]);
    }

    /**
     * Payment success page
     * GET /payment/{order_number}/success
     */
    public function success($orderNumber)
    {
        $data = [
            'title' => 'Pembayaran Berhasil',
            'order_number' => $orderNumber,
            'order' => null
        ];

        // Get order detail
        if (is_logged_in()) {
            $response = api_request("/user/order/{$orderNumber}", 'GET', [], true);
        } else {
            $response = api_request("/guest/orders/{$orderNumber}", 'GET');
        }

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } elseif (isset($response['order_number'])) {
            $data['order'] = $response;
        }

        return view('payment/success', $data);
    }
}