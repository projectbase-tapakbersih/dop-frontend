<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class GalleryController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form']);
    }

    /**
     * List all galleries
     * API: GET /api/galleries
     */
    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Manajemen Gallery',
            'galleries' => []
        ];

        $response = api_request('/galleries', 'GET', [], true);

        if (isset($response['success']) && $response['success']) {
            $data['galleries'] = $response['data'] ?? [];
        }

        return view('admin/gallery/index', $data);
    }

    /**
     * Create gallery for service
     * API: POST /api/services/{service}/gallery
     */
    public function create()
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        // Note: Gallery creation requires service_id
        $serviceId = $this->request->getPost('service_id');
        
        // For image upload, you would need to handle multipart/form-data
        // This is a simplified version
        $data = [
            'before_image' => $this->request->getPost('before_image'),
            'after_image' => $this->request->getPost('after_image'),
            'description' => $this->request->getPost('description')
        ];

        $response = api_request("/services/{$serviceId}/gallery", 'POST', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Update gallery
     * API: PUT /api/gallery/{gallery}
     */
    public function update($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = array_filter([
            'before_image' => $this->request->getPost('before_image'),
            'after_image' => $this->request->getPost('after_image'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active')
        ]);

        $response = api_request("/gallery/{$id}", 'PUT', $data, true);

        return $this->response->setJSON($response);
    }

    /**
     * Delete gallery
     * API: DELETE /api/gallery/{gallery}
     */
    public function delete($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $response = api_request("/gallery/{$id}", 'DELETE', [], true);

        return $this->response->setJSON($response);
    }

    /**
     * Activate gallery
     * API: PATCH /api/gallery/{gallery}/activate
     */
    public function activate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $response = api_request("/gallery/{$id}/activate", 'PATCH', [], true);

        return $this->response->setJSON($response);
    }

    /**
     * Deactivate gallery
     * API: PATCH /api/gallery/{gallery}/deactivate
     */
    public function deactivate($id)
    {
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $response = api_request("/gallery/{$id}/deactivate", 'PATCH', [], true);

        return $this->response->setJSON($response);
    }
}