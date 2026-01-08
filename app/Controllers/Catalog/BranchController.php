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
     * Branches List Page
     * GET /branches or /cabang
     * API: GET /api/branches
     */
    public function index()
    {
        $data = [
            'title' => 'Cabang Kami - Tirta Bersih Laundry',
            'branches' => [],
            'error' => null
        ];

        $response = api_request('/branches', 'GET', [], false);
        
        log_message('info', 'Public Branches Response: ' . json_encode($response));

        $branches = [];

        if (is_array($response)) {
            if (isset($response[0]) && is_array($response[0]) && isset($response[0]['id'])) {
                $branches = $response;
            } elseif (isset($response['success']) && $response['success']) {
                $responseData = $response['data'] ?? [];
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $branches = $responseData['data'];
                } elseif (is_array($responseData) && isset($responseData[0])) {
                    $branches = $responseData;
                }
            } elseif (isset($response['data']) && is_array($response['data'])) {
                $branches = $response['data'];
            } elseif (isset($response['message'])) {
                $data['error'] = $response['message'];
            }
        }

        // Filter active branches only
        $validBranches = [];
        foreach ($branches as $branch) {
            if (is_array($branch) && isset($branch['id'])) {
                if (!isset($branch['is_active']) || $branch['is_active']) {
                    $validBranches[] = $branch;
                }
            }
        }

        $data['branches'] = $validBranches;

        return view('catalog/branches/index', $data);
    }

    /**
     * Branch Detail Page
     * GET /branches/{id}
     * API: GET /api/branches/{id}
     */
    public function detail($id)
    {
        $data = [
            'title' => 'Detail Cabang - Tirta Bersih Laundry',
            'branch' => null,
            'error' => null
        ];

        $response = api_request("/branches/{$id}", 'GET', [], false);
        
        log_message('info', 'Branch Detail Response: ' . json_encode($response));

        if (is_array($response)) {
            if (isset($response['id'])) {
                $data['branch'] = $response;
                $data['title'] = ($response['name'] ?? 'Cabang') . ' - Tirta Bersih Laundry';
            } elseif (isset($response['success']) && $response['success']) {
                $data['branch'] = $response['data'] ?? null;
                if ($data['branch']) {
                    $data['title'] = ($data['branch']['name'] ?? 'Cabang') . ' - Tirta Bersih Laundry';
                }
            } elseif (isset($response['data'])) {
                $data['branch'] = $response['data'];
            } elseif (isset($response['message'])) {
                $data['error'] = $response['message'];
            }
        }

        if (!$data['branch']) {
            $data['error'] = 'Cabang tidak ditemukan';
        }

        return view('catalog/branches/detail', $data);
    }
}