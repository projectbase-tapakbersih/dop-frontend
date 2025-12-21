<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class BranchController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * List branches
     */
    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen Cabang',
            'branches' => [],
            'error' => null
        ];

        $response = api_request('/branches', 'GET', [], true);

        if (isset($response['success']) && $response['success']) {
            $data['branches'] = $response['data'] ?? [];
        }

        return view('admin/branches/index', $data);
    }

    /**
     * Create branch
     * API: POST /api/admin/branches
     */
    public function create()
    {
        if (!is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'coverage_radius_km' => $this->request->getPost('coverage_radius_km')
        ];

        $response = api_request('/admin/branches', 'POST', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Update branch
     * API: PUT /api/admin/branches/{branch}
     */
    public function update($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = array_filter([
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'coverage_radius_km' => $this->request->getPost('coverage_radius_km')
        ]);

        $response = api_request("/admin/branches/{$id}", 'PUT', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Delete branch
     * API: DELETE /api/admin/branches/{branch}
     */
    public function delete($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $response = api_request("/admin/branches/{$id}", 'DELETE', [], true);

        return $this->response->setJSON($response);
    }

    /**
     * Activate branch
     * API: PATCH /api/admin/branches/{branch}/activate
     */
    public function activate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $response = api_request("/admin/branches/{$id}/activate", 'PATCH', [], true);

        return $this->response->setJSON($response);
    }

    /**
     * Deactivate branch
     * API: PATCH /api/admin/branches/{branch}/deactivate
     */
    public function deactivate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $response = api_request("/admin/branches/{$id}/deactivate", 'PATCH', [], true);

        return $this->response->setJSON($response);
    }
}