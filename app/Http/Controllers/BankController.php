<?php

namespace App\Http\Controllers;

use App\Services\PaystackClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BankController extends ApiController
{
    /**
     * @var PaystackClient
     */
    private $client;

    public function __construct(PaystackClient $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        $banks = $this->client->getBanks();

        return $this->respond(['banks' => $banks]);
    }
}
