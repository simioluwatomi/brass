<?php


namespace App\Actions;

use App\DataTransferObjects\PaystackTransferObject;
use App\Models\Account;
use App\Models\TransactionEntry;
use App\Models\Webhook;
use App\Options\PaystackOptions;
use App\Options\TransactionEntryStatus;

class ProcessPaystackTransferWebhook
{
    public function execute(PaystackTransferObject $transfer, TransactionEntry $transactionEntry, array $data)
    {
        $transactionEntry->update([
            'status' => $transfer->status,
            'meta_data' => $transfer->toArray()
        ]);

        $transactionEntry->credit->first()->update([
            'status' => $transfer->status,
            'meta_data' => $transfer->toArray()
        ]);

        $account = Account::query()
            ->where('id', $transactionEntry->debit_account_id)
            ->lockForUpdate()
            ->first();

        $transfer->status == TransactionEntryStatus::SUCCESS
            ? $account->update(['book_balance' => $account->available_balance])
            : $account->update(['available_balance' => $account->book_balance]);

        Webhook::create(['vendor' => PaystackOptions::NAME, 'payload' => $data]);
    }

}
