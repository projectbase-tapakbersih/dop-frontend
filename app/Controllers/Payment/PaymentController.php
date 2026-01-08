<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;

class PaymentController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form', 'format']);
    }

    public function show($orderNumber)
    {
        $data = [
            'title'       => 'Pembayaran - ' . $orderNumber,
            'orderNumber' => $orderNumber,
            'order'       => null,
            'payment'     => null,
            'error'       => null,
        ];

        // Ambil order
        $response = null;
        if (is_logged_in()) {
            $response = api_request("/user/order/{$orderNumber}", 'GET', [], true);
        }
        if (!isset($response['success']) || !$response['success']) {
            $response = api_request("/guest/orders/{$orderNumber}", 'GET');
        }

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } elseif (is_array($response) && isset($response['order_number'])) {
            $data['order'] = $response;
        } else {
            $data['error'] = $response['message'] ?? 'Pesanan tidak ditemukan';
        }

        // Ambil payment status (kalau ada)
        if ($data['order']) {
            $paymentResponse = api_request("/payments/{$orderNumber}/status", 'GET', [], is_logged_in());
            if (isset($paymentResponse['success']) && $paymentResponse['success']) {
                $payment = $paymentResponse['data'] ?? $paymentResponse['payment'] ?? null;
                if (is_array($payment)) {
                    $payment = $this->normalizePayment($payment);
                }
                $data['payment'] = $payment;
            }
        }

        return view('payment/show', $data);
    }

    public function pay($orderNumber)
    {
        $channel = $this->request->getPost('channel');

        if (empty($channel)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Metode pembayaran harus dipilih']);
        }

        $allowed = ['qris', 'va_bni', 'va_mandiri'];
        if (!in_array($channel, $allowed, true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Metode pembayaran tidak didukung']);
        }

        $response = api_request("/payments/{$orderNumber}", 'POST', ['channel' => $channel], is_logged_in());

        if (isset($response['success']) && $response['success']) {
            $paymentData = $response['payment'] ?? $response['data'] ?? [];
            if (is_array($paymentData)) {
                $paymentData = $this->normalizePayment($paymentData);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $response['message'] ?? 'Pembayaran berhasil dibuat',
                'payment' => $paymentData,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal membuat pembayaran',
        ]);
    }

    public function process($orderNumber)
    {
        return $this->pay($orderNumber);
    }

    public function status($orderNumber)
    {
        $raw = api_request("/payments/{$orderNumber}/status", 'GET', [], is_logged_in());

        /**
         * Kita normalisasi semua kemungkinan bentuk response:
         * A) { success:true, payment:{...} }
         * B) { success:true, data:{...} }
         * C) { payment:{...} }
         * D) Callback-like Midtrans: { order_id, transaction_status, ... }  <-- ini kasus kamu
         */

        // Case A/B/C
        $payment = $raw['payment'] ?? $raw['data'] ?? null;

        // Case D: langsung bentuk callback midtrans (tidak ada payment/data)
        if (!is_array($payment) && is_array($raw) && isset($raw['transaction_status']) && isset($raw['order_id'])) {
            $payment = [
                'channel'             => ($raw['payment_type'] ?? null) === 'qris' ? 'qris' : null,
                'status'              => $raw['transaction_status'] ?? null,
                'transaction_status'  => $raw['transaction_status'] ?? null,
                'reference_id'        => $raw['transaction_id'] ?? null,
                'amount'              => $raw['gross_amount'] ?? null,
                'raw_response'        => json_encode($raw),
            ];
        }

        // Jika masih kosong, anggap gagal
        if (!is_array($payment)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $raw['message'] ?? 'Gagal mengambil status',
                'payment' => null,
                'debug'   => [
                    'raw_has_success' => isset($raw['success']) ? $raw['success'] : null,
                    'raw_keys'        => is_array($raw) ? array_keys($raw) : null,
                ],
            ]);
        }

        // Normalize payment supaya punya field penting (qris_payload, qr_string, va_number, etc)
        $payment = $this->normalizePayment($payment);

        // Pastikan transaction_status kebaca juga dari raw_response
        if (empty($payment['transaction_status']) && !empty($payment['raw_response']) && is_string($payment['raw_response'])) {
            $decoded = json_decode($payment['raw_response'], true);
            if (is_array($decoded) && !empty($decoded['transaction_status'])) {
                $payment['transaction_status'] = $decoded['transaction_status'];
            }
        }

        // Success = true kalau status endpoint tidak error
        // (meskipun raw tidak punya "success", kita anggap ok selama payment ter-parse)
        return $this->response->setJSON([
            'success' => true,
            'message' => $raw['message'] ?? 'OK',
            'payment' => $payment,
        ]);
    }



    public function cancel($orderNumber)
    {
        $response = api_request("/payments/{$orderNumber}/cancel", 'POST', [], is_logged_in());

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => $response['message'] ?? 'Pembayaran berhasil dibatalkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal membatalkan pembayaran']);
    }

    public function success($orderNumber)
    {
        $data = [
            'title'        => 'Pembayaran Berhasil',
            'order_number' => $orderNumber,
            'order'        => null
        ];

        $response = is_logged_in()
            ? api_request("/user/order/{$orderNumber}", 'GET', [], true)
            : api_request("/guest/orders/{$orderNumber}", 'GET');

        if (isset($response['success']) && $response['success']) {
            $data['order'] = $response['data'] ?? null;
        } elseif (is_array($response) && isset($response['order_number'])) {
            $data['order'] = $response;
        }

        return view('payment/success', $data);
    }

    private function normalizePayment(array $payment): array
    {
        // QRIS: pastikan qris_payload dan qr_string ada
        if (($payment['channel'] ?? null) === 'qris') {
            $qrString = $payment['qr_string'] ?? null;
            $qrisPayload = $payment['qris_payload'] ?? null;

            if (!empty($payment['raw_response']) && is_string($payment['raw_response'])) {
                $decoded = json_decode($payment['raw_response'], true);
                if (is_array($decoded)) {
                    if (!$qrString && !empty($decoded['qr_string'])) {
                        $qrString = $decoded['qr_string'];
                    }

                    // Cari URL QR dari actions kalau qris_payload tidak ada
                    if (!$qrisPayload && !empty($decoded['actions']) && is_array($decoded['actions'])) {
                        foreach ($decoded['actions'] as $act) {
                            if (!empty($act['url']) && is_string($act['url'])) {
                                // biasanya ada "generate-qr-code"
                                if (($act['name'] ?? '') === 'generate-qr-code') {
                                    $qrisPayload = $act['url'];
                                    break;
                                }
                                // fallback: ambil URL pertama yang mengandung "/qr-code"
                                if (strpos($act['url'], '/qr-code') !== false) {
                                    $qrisPayload = $act['url'];
                                }
                            }
                        }
                    }
                }
            }

            if ($qrString) $payment['qr_string'] = $qrString;
            if ($qrisPayload) $payment['qris_payload'] = $qrisPayload;
        }

        // VA: normalize va_number (mandiri sering di permata_va_number)
        if (isset($payment['channel']) && in_array($payment['channel'], ['va_bni', 'va_mandiri'], true)) {
            if (empty($payment['va_number']) && !empty($payment['raw_response']) && is_string($payment['raw_response'])) {
                $decoded = json_decode($payment['raw_response'], true);
                if (is_array($decoded)) {
                    if (!empty($decoded['permata_va_number'])) {
                        $payment['va_number'] = $decoded['permata_va_number'];
                    } elseif (!empty($decoded['va_numbers'][0]['va_number'])) {
                        $payment['va_number'] = $decoded['va_numbers'][0]['va_number'];
                    }
                }
            }
        }

        return $payment;
    }
}
