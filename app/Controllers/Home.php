<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Landing Page
     * Display: Hero, Featured Services, Gallery Preview, Branches
     */
    public function index()
    {
        $data = [
            'title' => 'Tapak Bersih - Layanan Perawatan Sepatu Profesional',
            'services' => [],
            'galleries' => [],
            'branches' => []
        ];

        // Get featured services (limit 6)
        $servicesResponse = api_request('/services', 'GET');
        if (isset($servicesResponse['success']) && $servicesResponse['success']) {
            $allServices = $servicesResponse['data'] ?? [];
            $data['services'] = array_slice($allServices, 0, 6);
        }

        // Get gallery preview (limit 6)
        $galleriesResponse = api_request('/galleries', 'GET');
        if (isset($galleriesResponse['success']) && $galleriesResponse['success']) {
            $allGalleries = $galleriesResponse['data'] ?? [];
            $data['galleries'] = array_slice($allGalleries, 0, 6);
        }

        // Get branches
        $branchesResponse = api_request('/branches', 'GET');
        if (isset($branchesResponse['success']) && $branchesResponse['success']) {
            $data['branches'] = $branchesResponse['data'] ?? [];
        }

        return view('home/index', $data);
    }
}