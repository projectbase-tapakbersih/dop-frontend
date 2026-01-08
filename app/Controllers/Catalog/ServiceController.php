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
     * Services List Page
     * GET /services or /layanan
     * API: GET /api/services
     */
    public function index()
    {
        $data = [
            'title' => 'Layanan Kami - Tirta Bersih Laundry',
            'services' => [],
            'error' => null
        ];

        $response = api_request('/services', 'GET', [], false);
        
        log_message('info', 'Public Services Response: ' . json_encode($response));

        $services = [];

        if (is_array($response)) {
            if (isset($response[0]) && is_array($response[0]) && isset($response[0]['id'])) {
                $services = $response;
            } elseif (isset($response['success']) && $response['success']) {
                $responseData = $response['data'] ?? [];
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $services = $responseData['data'];
                } elseif (is_array($responseData) && isset($responseData[0])) {
                    $services = $responseData;
                }
            } elseif (isset($response['data']) && is_array($response['data'])) {
                $services = $response['data'];
            } elseif (isset($response['message'])) {
                $data['error'] = $response['message'];
            }
        }

        // Filter active services only
        $validServices = [];
        foreach ($services as $service) {
            if (is_array($service) && isset($service['id'])) {
                // Only show active services on public page
                if (!isset($service['is_active']) || $service['is_active']) {
                    $validServices[] = $service;
                }
            }
        }

        $data['services'] = $validServices;

        return view('catalog/services/index', $data);
    }

    /**
     * Service Detail Page
     * GET /services/{id}
     * API: GET /api/services/{id}
     */
    public function detail($id)
    {
        $data = [
            'title' => 'Detail Layanan - Tirta Bersih Laundry',
            'service' => null,
            'error' => null
        ];

        $response = api_request("/services/{$id}", 'GET', [], false);
        
        log_message('info', 'Service Detail Response: ' . json_encode($response));

        if (is_array($response)) {
            if (isset($response['id'])) {
                $data['service'] = $response;
                $data['title'] = ($response['name'] ?? 'Layanan') . ' - Tirta Bersih Laundry';
            } elseif (isset($response['success']) && $response['success']) {
                $data['service'] = $response['data'] ?? null;
                if ($data['service']) {
                    $data['title'] = ($data['service']['name'] ?? 'Layanan') . ' - Tirta Bersih Laundry';
                }
            } elseif (isset($response['data'])) {
                $data['service'] = $response['data'];
            } elseif (isset($response['message'])) {
                $data['error'] = $response['message'];
            }
        }

        if (!$data['service']) {
            $data['error'] = 'Layanan tidak ditemukan';
        }

        return view('catalog/services/detail', $data);
    }
}