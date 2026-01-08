<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Gallery Management Controller
 * 
 * API Endpoints (sesuai Laravel routes):
 * - GET /api/gallery - List all galleries
 * - GET /api/gallery/{gallery} - Get gallery detail
 * - POST /api/admin/services/{service}/gallery - Create gallery (with images)
 * - PUT /api/admin/gallery/{gallery} - Update gallery
 * - DELETE /api/admin/gallery/{gallery} - Delete gallery
 * - PATCH /api/admin/gallery/{gallery}/activate - Activate gallery
 * - PATCH /api/admin/gallery/{gallery}/deactivate - Deactivate gallery
 */
class GalleryController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'form', 'format']);
    }

    /**
     * List all galleries
     */
    public function index()
    {
        if (!is_logged_in() || !is_admin()) {
            return redirect()->to('/auth/login')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Kelola Gallery - Admin',
            'galleries' => [],
            'services' => [],
            'error' => null
        ];

        // Fetch galleries - DIPERBAIKI: menggunakan /gallery bukan /galleries
        $response = api_request('/gallery', 'GET', [], false);
        log_message('info', 'Galleries Response: ' . json_encode($response));
        $data['galleries'] = $this->parseResponse($response);

        // Fetch services for dropdown
        $servicesResponse = api_request('/services', 'GET', [], false);
        $data['services'] = $this->parseResponse($servicesResponse);

        return view('admin/gallery/index', $data);
    }

    /**
     * Store new gallery
     * API: POST /api/admin/services/{service}/gallery
     */
    public function store()
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $serviceId = $this->request->getPost('service_id');
        $description = $this->request->getPost('description');
        $beforeImage = $this->request->getFile('before_image');
        $afterImage = $this->request->getFile('after_image');

        // Validate
        if (empty($serviceId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Service harus dipilih']);
        }

        if (!$beforeImage || !$beforeImage->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Before image harus diupload']);
        }

        if (!$afterImage || !$afterImage->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'After image harus diupload']);
        }

        // Prepare files for upload
        $files = [
            'before_image' => $beforeImage->getTempName(),
            'after_image' => $afterImage->getTempName()
        ];

        $postData = [
            'description' => $description ?? ''
        ];

        log_message('info', 'Gallery Store - Service ID: ' . $serviceId . ', Data: ' . json_encode($postData));

        // DIPERBAIKI: menggunakan endpoint /admin/services/{service}/gallery
        $response = api_upload("/admin/services/{$serviceId}/gallery", $files, $postData, true);
        
        log_message('info', 'Gallery Store Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Gallery berhasil ditambahkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal menambahkan gallery'
        ]);
    }

    /**
     * Update gallery
     * API: PUT /api/admin/gallery/{gallery}
     */
    public function update($id)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $serviceId = $this->request->getPost('service_id');
        $description = $this->request->getPost('description');
        $beforeImage = $this->request->getFile('before_image');
        $afterImage = $this->request->getFile('after_image');

        $postData = [
            'service_id' => $serviceId,
            'description' => $description ?? ''
        ];

        $files = [];

        // Add images if uploaded
        if ($beforeImage && $beforeImage->isValid()) {
            $files['before_image'] = $beforeImage->getTempName();
        }

        if ($afterImage && $afterImage->isValid()) {
            $files['after_image'] = $afterImage->getTempName();
        }

        log_message('info', 'Gallery Update - ID: ' . $id . ', Data: ' . json_encode($postData));

        // DIPERBAIKI: menggunakan /admin/gallery/{gallery} bukan /admin/galleries/{id}
        // If has new files, use upload endpoint
        if (!empty($files)) {
            $postData['_method'] = 'PUT'; // Laravel method spoofing
            $response = api_upload("/admin/gallery/{$id}", $files, $postData, true);
        } else {
            $response = api_request("/admin/gallery/{$id}", 'PUT', $postData, true);
        }
        
        log_message('info', 'Gallery Update Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Gallery berhasil diupdate'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal mengupdate gallery'
        ]);
    }

    /**
     * Delete gallery
     * API: DELETE /api/admin/gallery/{gallery}
     */
    public function delete($id)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        log_message('info', 'Gallery Delete - ID: ' . $id);

        // DIPERBAIKI: menggunakan /admin/gallery/{gallery} bukan /admin/galleries/{id}
        $response = api_request("/admin/gallery/{$id}", 'DELETE', [], true);
        
        log_message('info', 'Gallery Delete Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Gallery berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal menghapus gallery'
        ]);
    }

    /**
     * Activate gallery
     * API: PATCH /api/admin/gallery/{gallery}/activate
     */
    public function activate($id)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // DIPERBAIKI: menggunakan /admin/gallery/{gallery}/activate
        $response = api_request("/admin/gallery/{$id}/activate", 'PATCH', [], true);
        
        log_message('info', 'Gallery Activate Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Gallery berhasil diaktifkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal mengaktifkan gallery'
        ]);
    }

    /**
     * Deactivate gallery
     * API: PATCH /api/admin/gallery/{gallery}/deactivate
     */
    public function deactivate($id)
    {
        if (!is_logged_in() || !is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // DIPERBAIKI: menggunakan /admin/gallery/{gallery}/deactivate
        $response = api_request("/admin/gallery/{$id}/deactivate", 'PATCH', [], true);
        
        log_message('info', 'Gallery Deactivate Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Gallery berhasil dinonaktifkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Gagal menonaktifkan gallery'
        ]);
    }

    /**
     * Parse API response
     */
    private function parseResponse($response)
    {
        $items = [];

        if (is_array($response)) {
            if (isset($response[0]) && is_array($response[0])) {
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

        return $items;
    }
}