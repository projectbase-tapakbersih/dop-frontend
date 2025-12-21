<?php

namespace App\Controllers\Catalog;

use App\Controllers\BaseController;

class BranchController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Display all branches
     * API: GET /api/branches
     */
    public function index()
    {
        $data = [
            'title' => 'Lokasi Cabang Kami',
            'branches' => [],
            'error' => null
        ];

        $response = api_request('/branches', 'GET');

        log_message('info', 'Branches Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['branches'] = $response['data'] ?? [];
        } else {
            $data['error'] = $response['message'] ?? 'Gagal memuat cabang';
        }

        return view('catalog/branches/index', $data);
    }

    /**
     * Display only active branches
     * API: GET /api/branches/active
     */
    public function active()
    {
        $data = [
            'title' => 'Cabang Aktif',
            'branches' => [],
            'error' => null
        ];

        $response = api_request('/branches/active', 'GET');

        log_message('info', 'Active Branches Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['branches'] = $response['data'] ?? [];
        } else {
            $data['error'] = $response['message'] ?? 'Gagal memuat cabang aktif';
        }

        return view('catalog/branches/index', $data);
    }

    /**
     * Display branch detail
     * API: GET /api/branches/{id}
     */
    public function detail($id)
    {
        $data = [
            'title' => 'Detail Cabang',
            'branch' => null,
            'error' => null
        ];

        $response = api_request("/branches/{$id}", 'GET');

        log_message('info', "Branch {$id} Response: " . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            $data['branch'] = $response['data'] ?? null;
            $data['title'] = ($data['branch']['name'] ?? 'Detail Cabang') . ' - Tapak Bersih';
        } else {
            $data['error'] = $response['message'] ?? 'Cabang tidak ditemukan';
        }

        return view('catalog/branches/detail', $data);
    }
}