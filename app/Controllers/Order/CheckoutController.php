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
            'error' => null
        ];

        // Get services
        $servicesResponse = api_request('/services', 'GET');
        if (isset($servicesResponse['success']) && $servicesResponse['success']) {
            $data['services'] = $servicesResponse['data'] ?? [];
        }

        // Get active branches
        $branchesResponse = api_request('/branches/active', 'GET');
        if (isset($branchesResponse['success']) && $branchesResponse['success']) {
            $data['branches'] = $branchesResponse['data'] ?? [];
        }

        // Check if user is logged in or guest
        if (is_logged_in()) {
            $data['user'] = get_user_data();
        } elseif (is_guest()) {
            $data['user'] = get_user_data();
        }

        return view('order/create', $data);
    }

    /**
     * Process Checkout for Authenticated Users
     * API: POST /api/checkout
     */
    public function processCheckout()
    {
        if (!is_logged_in()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ]);
        }

        $rules = [
            'service_id' => 'required|numeric',
            'branch_id' => 'required|numeric',
            'pickup_address' => 'required',
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

        // Prepare checkout data
        $data = [
            'service_id' => $this->request->getPost('service_id'),
            'branch_id' => $this->request->getPost('branch_id'),
            'pickup_address' => $this->request->getPost('pickup_address'),
            'pickup_latitude' => $this->request->getPost('pickup_latitude'),
            'pickup_longitude' => $this->request->getPost('pickup_longitude'),
            'pickup_date' => $this->request->getPost('pickup_date'),
            'pickup_time' => $this->request->getPost('pickup_time'),
            'plastic_bag_confirmed' => $this->request->getPost('plastic_bag_confirmed') === 'true',
            'shoe_type' => $this->request->getPost('shoe_type'),
            'shoe_size' => $this->request->getPost('shoe_size'),
            'special_notes' => $this->request->getPost('special_notes'),
            'payment_method' => $this->request->getPost('payment_method')
        ];

        // Call API with authentication
        $response = api_request('/checkout', 'POST', $data, true);

        log_message('info', 'Checkout Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $orderNumber = $response['data']['order_number'] ?? null;
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'data' => $response['data'],
                'redirect' => base_url('order/detail/' . $orderNumber)
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
     * API: POST /api/guest/checkout
     */
    public function processGuestCheckout()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'phone' => 'required|numeric|min_length[10]',
            'service_id' => 'required|numeric',
            'branch_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Simple guest checkout data (sesuai dokumentasi API)
        $data = [
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'service_id' => $this->request->getPost('service_id'),
            'branch_id' => $this->request->getPost('branch_id')
        ];

        $response = api_request('/guest/checkout', 'POST', $data);

        log_message('info', 'Guest Checkout Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $orderNumber = $response['data']['order_number'] ?? null;
            
            // Store order number in session for guest
            session()->set('guest_order_number', $orderNumber);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'data' => $response['data'],
                'redirect' => base_url('guest/order/' . $orderNumber)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Checkout gagal',
            'errors' => $response['errors'] ?? []
        ]);
    }
}