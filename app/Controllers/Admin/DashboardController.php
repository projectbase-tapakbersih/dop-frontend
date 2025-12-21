<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    public function index()
    {
        if (!is_admin()) {
            return redirect()->to('/')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Admin Dashboard',
            'stats' => []
        ];

        // Get statistics from API if available
        // For now, just show the dashboard
        
        return view('admin/dashboard', $data);
    }
}