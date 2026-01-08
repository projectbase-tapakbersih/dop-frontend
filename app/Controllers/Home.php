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
     * Display: Hero, Services Summary, Features, Gallery Preview, Testimonials, CTA
     */
    public function index()
    {
        $data = [
            'title' => 'Tapak Bersih - Layanan Perawatan Sepatu Profesional',
            'services' => [],
            'galleries' => [],
            'branches' => [],
            'stats' => [
                'total_customers' => '1000+',
                'shoes_cleaned' => '5000+',
                'satisfaction_rate' => '99%',
                'branches' => '5+'
            ]
        ];

        // Get services (all for summary display)
        $servicesResponse = api_request('/services', 'GET');
        if (isset($servicesResponse['success']) && $servicesResponse['success']) {
            $data['services'] = $servicesResponse['data'] ?? [];
        }

        // Get gallery preview (limit 6 for showcase)
        $galleriesResponse = api_request('/galleries', 'GET');
        if (isset($galleriesResponse['success']) && $galleriesResponse['success']) {
            $allGalleries = $galleriesResponse['data'] ?? [];
            $data['galleries'] = array_slice($allGalleries, 0, 6);
        }

        // Get active branches
        $branchesResponse = api_request('/branches/active', 'GET');
        if (isset($branchesResponse['success']) && $branchesResponse['success']) {
            $data['branches'] = $branchesResponse['data'] ?? [];
        }

        return view('home/index', $data);
    }
}