<?php

namespace App\Controllers\Catalog;

use App\Controllers\BaseController;

/**
 * Catalog Gallery Controller
 * Public gallery page showing before/after images
 * 
 * API Endpoints:
 * - GET /api/galleries - List all galleries
 * - GET /api/galleries/{id} - Get gallery detail
 */
class GalleryController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url', 'format']);
    }

    /**
     * Gallery Page
     * GET /gallery
     * API: GET /api/galleries
     */
    public function index()
    {
        $data = [
            'title' => 'Gallery - Tapak Bersih',
            'gallery' => [],
            'error' => null
        ];

        // Try multiple API endpoints (some APIs use /gallery, others use /galleries)
        $response = api_request('/galleries', 'GET', [], false);
        
        // If /galleries fails, try /gallery
        if (!is_array($response) || (isset($response['success']) && !$response['success'])) {
            $response = api_request('/gallery', 'GET', [], false);
        }
        
        log_message('info', 'Gallery Response: ' . json_encode($response));

        $gallery = $this->parseResponse($response);

        // Filter only active galleries
        $activeGallery = [];
        foreach ($gallery as $item) {
            if (is_array($item) && isset($item['id'])) {
                // Check if gallery is active (default to true if not set)
                $isActive = $item['is_active'] ?? true;
                if ($isActive) {
                    $activeGallery[] = $item;
                }
            }
        }

        $data['gallery'] = $activeGallery;

        if (empty($activeGallery) && isset($response['message'])) {
            $data['error'] = $response['message'];
        }

        return view('catalog/gallery/index', $data);
    }

    /**
     * Gallery Detail Page
     * GET /gallery/{id}
     * API: GET /api/galleries/{id}
     */
    public function detail($id)
    {
        $data = [
            'title' => 'Detail Gallery - Tapak Bersih',
            'item' => null,
            'error' => null
        ];

        $response = api_request("/galleries/{$id}", 'GET', [], false);
        
        log_message('info', 'Gallery Detail Response: ' . json_encode($response));

        if (is_array($response)) {
            if (isset($response['success']) && $response['success']) {
                $data['item'] = $response['data'] ?? null;
            } elseif (isset($response['id'])) {
                $data['item'] = $response;
            } elseif (isset($response['data'])) {
                $data['item'] = $response['data'];
            } elseif (isset($response['message'])) {
                $data['error'] = $response['message'];
            }
        }

        if (!$data['item']) {
            return redirect()->to('/gallery')->with('error', 'Gallery tidak ditemukan');
        }

        return view('catalog/gallery/detail', $data);
    }

    /**
     * Parse API response to extract gallery items
     */
    private function parseResponse($response)
    {
        $items = [];

        if (is_array($response)) {
            // Direct array of items
            if (isset($response[0]) && is_array($response[0]) && isset($response[0]['id'])) {
                $items = $response;
            }
            // Success response with data
            elseif (isset($response['success']) && $response['success']) {
                $responseData = $response['data'] ?? [];
                
                // Paginated data
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $items = $responseData['data'];
                }
                // Direct array in data
                elseif (is_array($responseData) && isset($responseData[0])) {
                    $items = $responseData;
                }
                // Single item or empty
                elseif (is_array($responseData) && !empty($responseData)) {
                    $items = isset($responseData['id']) ? [$responseData] : $responseData;
                }
            }
            // Direct data without success field
            elseif (isset($response['data']) && is_array($response['data'])) {
                if (isset($response['data']['data'])) {
                    $items = $response['data']['data'];
                } else {
                    $items = $response['data'];
                }
            }
        }

        return $items;
    }
}