<?php

namespace App\Http\Controllers;

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
    public function __invoke(Request $request)
    {
        if (! $this->ensureValidSignature($request)) {
            return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->respondWithError('The signature is invalid.');
        }

        try {
            DB::beginTransaction();

            $transferObject = $this->client->getTransfer($request->input('data')['transfer_code']);

            $transactionEntry = TransactionEntry::query()
                ->with(['credit'])
                ->where('reference', $transferObject->reference)
                ->firstOrFail();

            if ($transactionEntry->status != TransactionEntryStatus::PENDING) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->respondWithError('Transaction is in its final state.');
            }

            $transactionEntry->update([
                'status' => $transferObject->status,
                'meta_data' => $transferObject->toArray()
            ]);

            $transactionEntry->credit->first()->update([
                'status' => $transferObject->status,
                'meta_data' => $transferObject->toArray()
            ]);

            $account = Account::query()
                ->where('id', $transactionEntry->debit_account_id)
                ->lockForUpdate()
                ->first();

            $transferObject->status == TransactionEntryStatus::SUCCESS
                ? $account->update(['book_balance' => $account->available_balance])
                : $account->update(['available_balance' => $account->book_balance]);

            Webhook::create(['vendor' => PaystackOptions::NAME, 'payload' => $request->all()]);

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
