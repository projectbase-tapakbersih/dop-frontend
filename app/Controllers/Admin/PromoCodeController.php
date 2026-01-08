<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Promo Code Management Controller
 * 
 * API Endpoints:
 * - GET /api/admin/promo-codes - List all promo codes
 * - GET /api/admin/promo-codes/{id} - Get promo code detail
 * - POST /api/admin/promo-codes - Create promo code
 * - PUT /api/admin/promo-codes/{id} - Update promo code
 * - DELETE /api/admin/promo-codes/{id} - Delete promo code
 * - PATCH /api/admin/promo-codes/{id}/toggle - Toggle active status
 * 
 * API Format:
 * {
 *   "code": "HEMAT20",
 *   "description": "Diskon 20%",
 *   "discount_type": "percentage",
 *   "discount_value": 20,
 *   "max_discount": 50000,
 *   "min_purchase": 100000,
 *   "quota": 100,
 *   "start_date": "2026-01-01",
 *   "end_date": "2026-01-31",
 *   "is_active": true
 * }
 */
class PromoCodeController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form', 'format']);
    }

    /**
     * List all promo codes
     */
    public function index()
    {
        if (!is_logged_in() || !is_admin()) {
            return redirect()->to('/auth/login')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Kelola Promo Code - Admin',
            'promo_codes' => [],
            'error' => null
        ];

        $response = api_request('/admin/promo-codes', 'GET', [], true);
        
        log_message('info', 'Promo Codes Response: ' . json_encode($response));

        $data['promo_codes'] = $this->parseResponse($response);

        if (isset($response['message']) && empty($data['promo_codes'])) {
            $data['error'] = $response['message'];
        }

        return view('admin/promo-codes/index', $data);
    }

    /**
     * Store new promo code
     * 
     * API expects:
     * - code: string
     * - description: string
     * - discount_type: "percentage" | "fixed"
     * - discount_value: number
     * - max_discount: number (nullable)
     * - min_purchase: number
     * - quota: number (nullable)
     * - start_date: "YYYY-MM-DD"
     * - end_date: "YYYY-MM-DD"
     * - is_active: boolean
     */
    public function store()
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $postData = [
            'code' => strtoupper($this->request->getPost('code')),
            'description' => $this->request->getPost('description') ?? '',
            'discount_type' => $this->request->getPost('discount_type'),
            'discount_value' => floatval($this->request->getPost('discount_value')),
            'max_discount' => $this->request->getPost('max_discount') ? floatval($this->request->getPost('max_discount')) : null,
            'min_purchase' => floatval($this->request->getPost('min_purchase') ?: 0),
            'quota' => $this->request->getPost('quota') ? intval($this->request->getPost('quota')) : null,
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'is_active' => $this->request->getPost('is_active') ? true : false
        ];

        // Validation
        if (empty($postData['code'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode promo harus diisi']);
        }

        if (empty($postData['discount_type']) || empty($postData['discount_value'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tipe dan nilai diskon harus diisi']);
        }

        // Remove null values
        $postData = array_filter($postData, function($value) {
            return $value !== null && $value !== '';
        });

        log_message('info', 'Promo Code Store - Data: ' . json_encode($postData));

        $response = api_request('/admin/promo-codes', 'POST', $postData, true);
        
        log_message('info', 'Promo Code Store Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Promo code berhasil ditambahkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal menambahkan promo code'
        ]);
    }

    /**
     * Show promo code detail
     */
    public function show($id)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/promo-codes/{$id}", 'GET', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $response['data'] ?? $response
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Promo code tidak ditemukan'
        ]);
    }

    /**
     * Update promo code
     */
    public function update($id)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $postData = [
            'code' => strtoupper($this->request->getPost('code')),
            'description' => $this->request->getPost('description') ?? '',
            'discount_type' => $this->request->getPost('discount_type'),
            'discount_value' => floatval($this->request->getPost('discount_value')),
            'max_discount' => $this->request->getPost('max_discount') ? floatval($this->request->getPost('max_discount')) : null,
            'min_purchase' => floatval($this->request->getPost('min_purchase') ?: 0),
            'quota' => $this->request->getPost('quota') ? intval($this->request->getPost('quota')) : null,
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'is_active' => $this->request->getPost('is_active') ? true : false
        ];

        log_message('info', 'Promo Code Update - ID: ' . $id . ', Data: ' . json_encode($postData));

        $response = api_request("/admin/promo-codes/{$id}", 'PUT', $postData, true);
        
        log_message('info', 'Promo Code Update Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Promo code berhasil diupdate'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal mengupdate promo code'
        ]);
    }

    /**
     * Delete promo code
     */
    public function delete($id)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        log_message('info', 'Promo Code Delete - ID: ' . $id);

        $response = api_request("/admin/promo-codes/{$id}", 'DELETE', [], true);
        
        log_message('info', 'Promo Code Delete Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Promo code berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal menghapus promo code'
        ]);
    }

    /**
     * Toggle promo code active status
     */
    public function toggle($id)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        log_message('info', 'Promo Code Toggle - ID: ' . $id);

        $response = api_request("/admin/promo-codes/{$id}/toggle", 'PATCH', [], true);
        
        log_message('info', 'Promo Code Toggle Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status promo code berhasil diubah'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal mengubah status promo code'
        ]);
    }

    /**
     * Parse API response
     */
    private function parseResponse($response)
    {
        $items = [];

        if (is_array($response)) {
            if (isset($response[0]) && is_array($response[0]) && isset($response[0]['id'])) {
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

        // Filter valid items
        $validItems = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item['id'])) {
                $validItems[] = $item;
            }
        }

        return $validItems;
    }
}