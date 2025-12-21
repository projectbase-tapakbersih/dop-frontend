<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ServiceController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen Layanan',
            'services' => []
        ];

        $response = api_request('/services', 'GET', [], true);

        if (isset($response['success']) && $response['success']) {
            $data['services'] = $response['data'] ?? [];
        }

        return view('admin/services/index', $data);
    }

    /**
     * Create service
     * API: POST /api/services
     */
    public function create()
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'duration_hours' => $this->request->getPost('duration_hours')
        ];

        $response = api_request('/services', 'POST', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Update service
     * API: PUT /api/services/{service}
     */
    public function update($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = array_filter([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'duration_hours' => $this->request->getPost('duration_hours')
        ]);

        $response = api_request("/services/{$id}", 'PUT', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Delete service
     * API: DELETE /api/services/{service}
     */
    public function delete($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $response = api_request("/services/{$id}", 'DELETE', [], true);

        return $this->response->setJSON($response);
    }

    /**
     * Activate service
     * API: PATCH /api/services/{service}/activate
     */
    public function activate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $response = api_request("/services/{$id}/activate", 'PATCH', [], true);

        return $this->response->setJSON($response);
    }

    /**
     * Deactivate service
     * API: PATCH /api/services/{service}/deactivate
     */
    public function deactivate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $response = api_request("/services/{$id}/deactivate", 'PATCH', [], true);

        return $this->response->setJSON($response);
    }
}