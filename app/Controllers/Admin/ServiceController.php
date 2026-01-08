<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Service Controller
 * 
 * API Endpoints:
 * - GET /api/services - List all services
 * - POST /api/admin/services - Create service
 * - PUT /api/admin/services/{id} - Update service
 * - DELETE /api/admin/services/{id} - Delete service
 * - PATCH /api/admin/services/{id}/activate - Activate
 * - PATCH /api/admin/services/{id}/deactivate - Deactivate
 */
class ServiceController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * List all services
     * API: GET /api/services
     */
    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen Layanan - Admin',
            'services' => [],
            'error' => null
        ];

        $response = api_request('/services', 'GET', [], false);
        
        $services = $this->parseResponse($response);
        $data['services'] = $services;

        if (isset($response['message']) && empty($services)) {
            $data['error'] = $response['message'];
        }

        return view('admin/services/index', $data);
    }

    /**
     * Store new service
     * API: POST /api/admin/services
     * 
     * Format:
     * {
     *   "name": "Dripsole Sepatu",
     *   "description": "Perlindungan sepatu dari air",
     *   "price": 1000,
     *   "duration_hours": 24
     * }
     */
    public function store()
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $postData = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => (int)$this->request->getPost('price'),
            'duration_hours' => (int)$this->request->getPost('duration_hours')
        ];

        log_message('info', 'Service Store Data: ' . json_encode($postData));

        $response = api_request('/admin/services', 'POST', $postData, true);
        
        log_message('info', 'Service Store Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil ditambahkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal menambahkan layanan']);
    }

    /**
     * Update service
     * API: PUT /api/admin/services/{id}
     */
    public function update($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $postData = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => (int)$this->request->getPost('price'),
            'duration_hours' => (int)$this->request->getPost('duration_hours')
        ];

        log_message('info', 'Service Update Data: ' . json_encode($postData));

        $response = api_request("/admin/services/{$id}", 'PUT', $postData, true);
        
        log_message('info', 'Service Update Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil diupdate']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal mengupdate layanan']);
    }

    /**
     * Delete service
     * API: DELETE /api/admin/services/{id}
     */
    public function delete($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/services/{$id}", 'DELETE', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal menghapus layanan']);
    }

    /**
     * Activate service
     * API: PATCH /api/admin/services/{id}/activate
     */
    public function activate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/services/{$id}/activate", 'PATCH', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil diaktifkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal mengaktifkan layanan']);
    }

    /**
     * Deactivate service
     * API: PATCH /api/admin/services/{id}/deactivate
     */
    public function deactivate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/services/{$id}/deactivate", 'PATCH', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil dinonaktifkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal menonaktifkan layanan']);
    }

    /**
     * Parse API response to array
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
                }
            } elseif (isset($response['data']) && is_array($response['data'])) {
                if (isset($response['data']['data'])) {
                    $items = $response['data']['data'];
                } else {
                    $items = $response['data'];
                }
            }
        }

        // Validate items
        $validItems = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item['id'])) {
                $validItems[] = $item;
            }
        }

        return $validItems;
    }
}