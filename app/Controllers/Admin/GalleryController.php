<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;

class GalleryController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Gallery page
     * GET /gallery
     * API: GET /api/gallery
     */
    public function index()
    {
        $data = [
            'title' => 'Gallery - Tirta Bersih Laundry',
            'gallery' => [],
            'error' => null
        ];

        // API: GET /api/gallery (public endpoint)
        $response = api_request('/gallery', 'GET', [], false);
        
        log_message('info', 'Gallery Response: ' . json_encode($response));

        $gallery = [];

        if (is_array($response)) {
            if (isset($response[0]) && is_array($response[0]) && isset($response[0]['id'])) {
                $gallery = $response;
            } elseif (isset($response['success']) && $response['success']) {
                $responseData = $response['data'] ?? [];
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $gallery = $responseData['data'];
                } elseif (is_array($responseData) && isset($responseData[0])) {
                    $gallery = $responseData;
                }
            } elseif (isset($response['data']) && is_array($response['data'])) {
                $gallery = $response['data'];
            } elseif (isset($response['message'])) {
                $data['error'] = $response['message'];
            }
        }

        $validGallery = [];
        foreach ($gallery as $item) {
            if (is_array($item) && isset($item['id'])) {
                $validGallery[] = $item;
            }
        }

        $data['gallery'] = $validGallery;

        return view('public/gallery', $data);
    }
}