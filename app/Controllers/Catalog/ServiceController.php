<?php

namespace App\Controllers\Catalog;

use App\Controllers\BaseController;

class ServiceController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Display all services
     * API: GET /api/services
     */
    public function index()
    {
        $data = [
            'title' => 'Katalog Layanan - Tapak Bersih',
            'services' => [],
            'error' => null
        ];

        // Get all services from API
        $response = api_request('/services', 'GET');

        log_message('info', 'Services Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['services'] = $response['data'] ?? [];
        } else {
            $data['error'] = $response['message'] ?? 'Gagal memuat layanan';
            log_message('error', 'Failed to load services: ' . json_encode($response));
        }

        return view('catalog/services/index', $data);
    }

    /**
     * Display service detail with gallery
     * API: GET /api/services/{id}
     * API: GET /api/services/{id}/gallery
     */
    public function detail($id)
    {
        $data = [
            'title' => 'Detail Layanan',
            'service' => null,
            'gallery' => [],
            'error' => null
        ];

        // Get service detail
        $serviceResponse = api_request("/services/{$id}", 'GET');

        log_message('info', "Service {$id} Response: " . json_encode($serviceResponse));

        if (isset($serviceResponse['success']) && $serviceResponse['success']) {
            $data['service'] = $serviceResponse['data'] ?? null;
            $data['title'] = ($data['service']['name'] ?? 'Detail Layanan') . ' - Tapak Bersih';

            // Get gallery for this service
            $galleryResponse = api_request("/services/{$id}/gallery", 'GET');
            
            log_message('info', "Service {$id} Gallery Response: " . json_encode($galleryResponse));
            
            if (isset($galleryResponse['success']) && $galleryResponse['success']) {
                $data['gallery'] = $galleryResponse['data'] ?? [];
            }
        } else {
            $data['error'] = $serviceResponse['message'] ?? 'Layanan tidak ditemukan';
        }

        return view('catalog/services/detail', $data);
    }
}