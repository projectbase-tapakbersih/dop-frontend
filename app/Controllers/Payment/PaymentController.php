<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;

class PaymentController extends BaseController
{
    public function __construct()
    {
        helper(['api', 'url']);
    }

    /**
     * Payment landing / information page
     */
    public function index()
    {
        $data = [
            'title' => 'Pembayaran',
            'info' => null
        ];

        // Optionally display payment instructions or check order status
        return view('payment/index', $data);
    }

    /**
     * Simple payment callback handler (placeholder)
     */
    public function callback()
    {
        // Implementation depends on payment gateway
        $payload = $this->request->getPost();
        log_message('info', 'Payment callback received: ' . json_encode($payload));

        return service('response')->setStatusCode(200)->setBody('OK');
    }
}
