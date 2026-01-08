<?php

namespace App\Controllers\Order;

use App\Controllers\BaseController;

class CheckoutController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * Show checkout page
     */
    public function createPage()
    {
        $data = [
            'title' => 'Buat Pesanan - Tapak Bersih',
            'services' => [],
            'branches' => [],
            'error' => null,
            'selected_service' => null,
            'selected_branch' => null
        ];

        // Get services - API: GET /api/services
        $servicesResponse = api_request('/services', 'GET');
        if (isset($servicesResponse['success']) && $servicesResponse['success']) {
            $data['services'] = $servicesResponse['data'] ?? [];
        } elseif (is_array($servicesResponse) && isset($servicesResponse[0])) {
            $data['services'] = $servicesResponse;
        } else {
            $data['error'] = 'Gagal memuat layanan. Silakan refresh halaman.';
        }

        // Get active branches - API: GET /api/branches/active
        $branchesResponse = api_request('/branches/active', 'GET');
        if (isset($branchesResponse['success']) && $branchesResponse['success']) {
            $data['branches'] = $branchesResponse['data'] ?? [];
        } elseif (is_array($branchesResponse) && isset($branchesResponse[0])) {
            $data['branches'] = $branchesResponse;
        }

        // Pre-select service if passed via query param
        $serviceId = $this->request->getGet('service');
        if ($serviceId && !empty($data['services'])) {
            foreach ($data['services'] as $service) {
                if (($service['id'] ?? null) == $serviceId) {
                    $data['selected_service'] = $service;
                    break;
                }
            }
        }

        // Pre-select branch if passed via query param
        $branchId = $this->request->getGet('branch');
        if ($branchId && !empty($data['branches'])) {
            foreach ($data['branches'] as $branch) {
                if (($branch['id'] ?? null) == $branchId) {
                    $data['selected_branch'] = $branch;
                    break;
                }
            }
        }

        // Check if user is logged in or guest
        if (function_exists('is_logged_in') && is_logged_in()) {
            $userData = function_exists('get_user_data') ? get_user_data() : null;
            $data['user'] = is_array($userData) ? $userData : null;
            $data['is_guest'] = false;
        } else {
            // Guest = not logged in
            $data['user'] = null;
            $data['is_guest'] = true;
        }

        return view('order/create', $data);
    }

    /**
     * Process Checkout for Authenticated Users
     * POST /order/checkout
     * API: POST /api/user/checkout
     */
    public function processCheckout()
    {
        if (!is_logged_in()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu',
                'require_login' => true,
                'redirect' => base_url('auth/login')
            ]);
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'phone' => 'required|min_length[10]',
            'service_id' => 'required|numeric',
            'branch_id' => 'required|numeric',
            'pickup_address' => 'required|min_length[10]',
            'pickup_date' => 'required',
            'pickup_time' => 'required',
            'plastic_bag_confirmed' => 'required',
            'shoe_type' => 'required',
            'payment_method' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $payload = [
            'guest_name' => $this->request->getPost('name'),
            'guest_phone' => $this->request->getPost('phone'),
            'branch_id' => (int) $this->request->getPost('branch_id'),
            'pickup_address' => $this->request->getPost('pickup_address'),
            'pickup_latitude' => $this->request->getPost('pickup_latitude') ?: -7.2575,
            'pickup_longitude' => $this->request->getPost('pickup_longitude') ?: 112.7521,
            'pickup_date' => $this->request->getPost('pickup_date'),
            'pickup_time' => $this->request->getPost('pickup_time'),
            'plastic_bag_confirmed' => $this->request->getPost('plastic_bag_confirmed') === 'true' || $this->request->getPost('plastic_bag_confirmed') === '1',
            'payment_method' => $this->request->getPost('payment_method'), // (qris/transfer) — detail channel dipilih di halaman payment
            'items' => [
                [
                    'service_id' => (int) $this->request->getPost('service_id'),
                    'shoe_type' => $this->request->getPost('shoe_type'),
                    'shoe_size' => $this->request->getPost('shoe_size') ?: null,
                    'special_notes' => $this->request->getPost('special_notes') ?: null,
                    'quantity' => 1
                ]
            ]
        ];

        log_message('info', 'User Checkout Request: ' . json_encode($payload));

        $response = api_request('/user/checkout', 'POST', $payload, true);

        log_message('info', 'User Checkout Response: ' . json_encode($response));

        // ✅ Normalisasi order_number dari berbagai kemungkinan response:
        // - { success:true, data:{order_number:"TB-..."} }
        // - { success:true, order_number:"TB-..." }
        // - { success:true, order_number:"TB-...", total_amount:... } (seperti Postman kamu)
        if (isset($response['success']) && $response['success']) {
            $orderNumber =
                ($response['data']['order_number'] ?? null)
                ?? ($response['order_number'] ?? null)
                ?? (($response['data']['orderNumber'] ?? null));

            if (!$orderNumber) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Checkout sukses, tetapi order number tidak ditemukan dari response API.'
                ]);
            }

            // ✅ Redirect langsung ke halaman payment
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'data' => $response['data'] ?? $response,
                'order_number' => $orderNumber,
                'redirect' => base_url('payment/' . $orderNumber)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Checkout gagal',
            'errors' => $response['errors'] ?? []
        ]);
    }

    /**
     * Process Guest Checkout
     * POST /order/guest-checkout
     * API: POST /api/guest/orders
     */
    public function processGuestCheckout()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'phone' => 'required|min_length[10]',
            'service_id' => 'required|numeric',
            'branch_id' => 'required|numeric',
            'pickup_address' => 'required|min_length[10]',
            'pickup_date' => 'required',
            'pickup_time' => 'required',
            'shoe_type' => 'required',
            'plastic_bag_confirmed' => 'required',
            'payment_method' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $payload = [
            'guest_name' => $this->request->getPost('name'),
            'guest_phone' => $this->request->getPost('phone'),
            'branch_id' => (int) $this->request->getPost('branch_id'),
            'pickup_address' => $this->request->getPost('pickup_address'),
            'pickup_latitude' => $this->request->getPost('pickup_latitude') ?: -7.2575,
            'pickup_longitude' => $this->request->getPost('pickup_longitude') ?: 112.7521,
            'pickup_date' => $this->request->getPost('pickup_date'),
            'pickup_time' => $this->request->getPost('pickup_time'),
            'plastic_bag_confirmed' => $this->request->getPost('plastic_bag_confirmed') === 'true' || $this->request->getPost('plastic_bag_confirmed') === '1',
            'payment_method' => $this->request->getPost('payment_method'),
            'items' => [
                [
                    'service_id' => (int) $this->request->getPost('service_id'),
                    'shoe_type' => $this->request->getPost('shoe_type'),
                    'shoe_size' => $this->request->getPost('shoe_size') ?: null,
                    'special_notes' => $this->request->getPost('special_notes') ?: null,
                    'quantity' => 1
                ]
            ]
        ];

        log_message('info', 'Guest Checkout Request: ' . json_encode($payload));

        $response = api_request('/guest/orders', 'POST', $payload);

        log_message('info', 'Guest Checkout Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $orderNumber =
                ($response['data']['order_number'] ?? null)
                ?? ($response['order_number'] ?? null)
                ?? (($response['data']['orderNumber'] ?? null));

            if (!$orderNumber) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Checkout sukses, tetapi order number tidak ditemukan dari response API.'
                ]);
            }

            // ✅ Simpan untuk tracking guest
            session()->set('guest_order_number', $orderNumber);
            session()->set('guest_phone', $this->request->getPost('phone'));

            // ✅ Redirect langsung ke halaman payment
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'data' => $response['data'] ?? $response,
                'order_number' => $orderNumber,
                'redirect' => base_url('payment/' . $orderNumber)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Checkout gagal',
            'errors' => $response['errors'] ?? []
        ]);
    }
}
