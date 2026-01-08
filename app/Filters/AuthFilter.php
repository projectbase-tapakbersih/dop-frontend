<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('api');
        
        // Check if user is logged in
        if (!is_logged_in()) {
            // Check if this is an AJAX request
            $isAjax = $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' 
                   || $request->getHeaderLine('Accept') === 'application/json'
                   || strpos($request->getHeaderLine('Content-Type'), 'application/x-www-form-urlencoded') !== false;
            
            if ($isAjax) {
                // Return JSON response for AJAX requests
                $response = service('response');
                return $response->setJSON([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu',
                    'redirect' => base_url('auth/login'),
                    'require_login' => true
                ])->setStatusCode(401);
            }
            
            // Store intended URL for redirect after login
            $intendedUrl = current_url();
            session()->set('redirect_url', $intendedUrl);
            
            // Redirect to login with message
            return redirect()->to('/auth/login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}