<?php

namespace App\Http\Controllers;

use App\Actions\CreateExternalTransactionEntries;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionEntryResource;
use App\Models\Account;
use Illuminate\Http\Client\HttpClientException;
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
     * @param StoreTransactionRequest          $request
     * @param CreateExternalTransactionEntries $createExternalTransactionEntries
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTransactionRequest $request, CreateExternalTransactionEntries $createExternalTransactionEntries)
    {
        try {
            DB::beginTransaction();

            $debitAccount = Account::query()
                ->with('type')
                ->where('number', $request->input('debit_account_number'))
                ->lockForUpdate()
                ->first();

            if ($request->input('amount') > $debitAccount->available_balance) {
                throw ValidationException::withMessages(['amount' => 'Insufficient account balance']);
            }

            $debitTransactionEntry = $createExternalTransactionEntries->execute(
                $debitAccount,
                $request->merge(['currency' => $debitAccount->type->currency])->all()
            );

            DB::commit();

            return $this->respondCreated(['transaction_entry' => new TransactionEntryResource($debitTransactionEntry)]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            if ($exception instanceof ValidationException || $exception instanceof HttpClientException) {
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
