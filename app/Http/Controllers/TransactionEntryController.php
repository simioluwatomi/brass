<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionEntryResource;
use App\Models\Account;
use App\Models\TransactionEntry;
use App\Options\PaystackOptions;
use App\Options\TransactionEntryTypes;
use App\Options\TransactionEntryStatus;
use App\Services\PaystackClient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class TransactionEntryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTransactionRequest $request
     * @param PaystackClient          $client
      */
    public function store(StoreTransactionRequest $request, PaystackClient $client)
    {
        try {
            DB::beginTransaction();

            // Fetch the debit account from the database and lock it
            // Revalidate the account's available balance
            // This makes it possible to catch changes in the account's balance
            // Between the time of initial validation till now
            $debitAccount = Account::query()
                ->with('type')
                ->where('number', $request->input('debit_account_number'))
                ->lockForUpdate()
                ->first();

            if ($request->input('amount') > $debitAccount->available_balance) {
                throw ValidationException::withMessages(['amount' => 'Insufficient account balance']);
            }

            $paystackRecipient = $client->createTransferRecipient([
                "type" => PaystackOptions::TRANSFER_RECIPIENT_TYPE,
                "name" => $request->input('credit_account_name'),
                "account_number" => $request->input('credit_account_number'),
                "bank_code" => $request->input('credit_bank_code'),
                "currency" => $debitAccount->type->currency
            ]);

            $paystackTransfer = $client->transfer([
                "source" => PaystackOptions::TRANSFER_SOURCE,
                "reason" => $request->input('description'),
                "amount" => $request->input('amount'),
                "recipient" => $paystackRecipient->recipient_code,
                "reference" => $request->input('reference')
            ]);

            $debitTransactionEntry = TransactionEntry::create([
                'debit_account_id' => $debitAccount->id,
                'external_account_name' => $paystackRecipient->account_details->account_name,
                'external_account_number' => $paystackRecipient->account_details->account_number,
                'external_bank_code' => $paystackRecipient->account_details->bank_code,
                'amount' => -$request->input('amount'),
                'reference' => $request->input('reference'),
                'description' => $request->input('description'),
                'type' => TransactionEntryTypes::DEBIT,
                'status' => $paystackTransfer->status,
                'meta_data' => $paystackTransfer->toArray(),
            ]);

            $creditTransactionEntry = TransactionEntry::create([
                'credit_account_id' => Account::whereNumber(config('accounts.nuban_settlement_account'))->first()->id,
                'external_account_name' => $paystackRecipient->account_details->account_name,
                'external_account_number' => $paystackRecipient->account_details->account_number,
                'external_bank_code' => $paystackRecipient->account_details->bank_code,
                'amount' => $request->input('amount'),
                'reference' => Str::uuid()->getHex()->toString(),
                'description' => $request->input('description'),
                'type' => TransactionEntryTypes::CREDIT,
                'status' => $paystackTransfer->status,
                'meta_data' => $paystackTransfer->toArray(),
            ]);

            $debitTransactionEntry->credit()->sync($creditTransactionEntry->id);

            $balance = $debitAccount->available_balance + $debitTransactionEntry->amount;

            if ($paystackTransfer->status == TransactionEntryStatus::PENDING) {
                $debitAccount->update(['available_balance' => $balance]);
            }

            if ($paystackTransfer->status == TransactionEntryStatus::SUCCESS) {
                $debitAccount->update(['available_balance' => $balance, 'book_balance' => $balance]);
            }

            DB::commit();

            return $this->respondCreated(['transaction_entry' => new TransactionEntryResource($debitTransactionEntry)]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            Log::error($exception);

            return $this->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE)
                ->respondWithError('Something went wrong. Please try again');
        }

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

}
