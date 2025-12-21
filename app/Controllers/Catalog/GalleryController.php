<?php

namespace App\Controllers\Catalog;

use App\Controllers\BaseController;

class GalleryController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Display all galleries
     * API: GET /api/galleries
     */
    public function index()
    {
        $data = [
            'title' => 'Gallery - Hasil Kerja Kami',
            'galleries' => [],
            'error' => null
        ];

        $response = api_request('/galleries', 'GET');

        log_message('info', 'Galleries Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['galleries'] = $response['data'] ?? [];
        } else {
            $data['error'] = $response['message'] ?? 'Gagal memuat gallery';
        }

        return view('catalog/gallery/index', $data);
    }

    /**
     * Display single gallery detail
     * API: GET /api/gallery/{id}
     */
    public function detail($id)
    {
        $data = [
            'title' => 'Gallery Detail',
            'gallery' => null,
            'error' => null
        ];

        $response = api_request("/gallery/{$id}", 'GET');

        log_message('info', "Gallery {$id} Response: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['gallery'] = $response['data'] ?? null;
        } else {
            $data['error'] = $response['message'] ?? 'Gallery tidak ditemukan';
        }

        return view('catalog/gallery/detail', $data);
    }
}