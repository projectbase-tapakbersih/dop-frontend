<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('api');
        
        // Check if user is logged in
        if (!is_logged_in()) {
            return redirect()->to('/auth/login')
                ->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Check if user is admin
        if (!is_admin()) {
            return redirect()->to('/')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}