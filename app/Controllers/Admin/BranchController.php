<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Branch Controller
 * 
 * API Endpoints:
 * - GET /api/branches - List all branches
 * - POST /api/admin/branches - Create branch
 * - PUT /api/admin/branches/{id} - Update branch
 * - DELETE /api/admin/branches/{id} - Delete branch
 * - PATCH /api/admin/branches/{id}/activate - Activate
 * - PATCH /api/admin/branches/{id}/deactivate - Deactivate
 */
class BranchController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * List all branches
     * API: GET /api/branches
     */
    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen Cabang - Admin',
            'branches' => [],
            'error' => null
        ];

        $response = api_request('/branches', 'GET', [], false);
        
        $branches = $this->parseResponse($response);
        $data['branches'] = $branches;

        if (isset($response['message']) && empty($branches)) {
            $data['error'] = $response['message'];
        }

        return view('admin/branches/index', $data);
    }

    /**
     * Store new branch
     * API: POST /api/admin/branches
     * 
     * Format:
     * {
     *   "name": "Tapak Bersih Jember",
     *   "address": "Jl. Soekarno Hatta",
     *   "latitude": -7.9666,
     *   "longitude": 112.6326,
     *   "coverage_radius_km": 15
     * }
     */
    public function store()
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $postData = [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'latitude' => (float)$this->request->getPost('latitude'),
            'longitude' => (float)$this->request->getPost('longitude'),
            'coverage_radius_km' => (float)$this->request->getPost('coverage_radius_km')
        ];

        log_message('info', 'Branch Store Data: ' . json_encode($postData));

        $response = api_request('/admin/branches', 'POST', $postData, true);
        
        log_message('info', 'Branch Store Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cabang berhasil ditambahkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal menambahkan cabang']);
    }

    /**
     * Update branch
     * API: PUT /api/admin/branches/{id}
     */
    public function update($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $postData = [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'latitude' => (float)$this->request->getPost('latitude'),
            'longitude' => (float)$this->request->getPost('longitude'),
            'coverage_radius_km' => (float)$this->request->getPost('coverage_radius_km')
        ];

        log_message('info', 'Branch Update Data: ' . json_encode($postData));

        $response = api_request("/admin/branches/{$id}", 'PUT', $postData, true);
        
        log_message('info', 'Branch Update Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cabang berhasil diupdate']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal mengupdate cabang']);
    }

    /**
     * Delete branch
     * API: DELETE /api/admin/branches/{id}
     */
    public function delete($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/branches/{$id}", 'DELETE', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cabang berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal menghapus cabang']);
    }

    /**
     * Activate branch
     * API: PATCH /api/admin/branches/{id}/activate
     */
    public function activate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/branches/{$id}/activate", 'PATCH', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cabang berhasil diaktifkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal mengaktifkan cabang']);
    }

    /**
     * Deactivate branch
     * API: PATCH /api/admin/branches/{id}/deactivate
     */
    public function deactivate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $response = api_request("/admin/branches/{$id}/deactivate", 'PATCH', [], true);
        
        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cabang berhasil dinonaktifkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $response['message'] ?? 'Gagal menonaktifkan cabang']);
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

        $validItems = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item['id'])) {
                $validItems[] = $item;
            }
        }

        return $validItems;
    }
}