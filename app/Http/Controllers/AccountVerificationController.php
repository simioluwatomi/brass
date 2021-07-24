<?php

namespace App\Http\Controllers;

use App\Services\PaystackClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AccountVerificationController extends ApiController
{
    /**
     * @var PaystackClient
     */
    private $client;

    public function __construct(PaystackClient $client)
    {
        $this->client = $client;
    }

    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
           'account_number' => ['required', 'string'],
           'bank_code' => ['required', 'string']
        ]);

        try {
            $account = $this->client->verifyAccount(
                $request->input('account_number'),
                $request->input('bank_code')
            );
        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE)
                ->respondWithError('Something went wrong. Please try again');
        }

        return $this->respond(['account' => $account]);
    }
}
