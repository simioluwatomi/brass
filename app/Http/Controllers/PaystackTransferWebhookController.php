<?php

namespace App\Http\Controllers;

use App\Actions\ProcessPaystackTransferWebhook;
use App\Actions\ValidatePaystackTransferWebhook;
use App\Models\Account;
use App\Models\TransactionEntry;
use App\Models\Webhook;
use App\Options\PaystackOptions;
use App\Options\TransactionEntryStatus;
use App\Services\PaystackClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaystackTransferWebhookController extends ApiController
{
    /**
     * @var PaystackClient
     */
    private $client;

    public function __construct(PaystackClient $client)
    {
        $this->client = $client;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(
        Request $request,
        ProcessPaystackTransferWebhook $processWebhook)
    {
        if (! $this->ensureValidSignature($request)) {
            return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->respondWithError('The webhook signature is invalid.');
        }

        $transferObject = $this->client->getTransfer($request->input('data')['reference']);

        $transactionEntry = TransactionEntry::query()
            ->with(['credit'])
            ->where('reference', $transferObject->reference)
            ->firstOrFail();

        if ($transactionEntry->status != TransactionEntryStatus::PENDING) {
            return $this->setStatusCode(Response::HTTP_FORBIDDEN)
                ->respondWithError('Transaction is in its final state.');
        }

        try {
            DB::beginTransaction();

            $processWebhook->execute($transferObject, $transactionEntry, $request->all());

            DB::commit();

            return response()->json([], $this->getStatusCode());
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error($exception);

            return $this->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE)
                ->respondWithError('Something went wrong. Please try again');
        }
    }

    private function ensureValidSignature(Request $request)
    {
        $header = $request->header(PaystackOptions::WEBHOOK_HEADER);

        if (! $header) {
            return false;
        }

        $computedHash = hash_hmac('sha512', $request->getContent(), config('services.paystack.secret_key'));

        return hash_equals($computedHash, $header);
    }
}
