<?php

namespace App\Actions;

use App\DataTransferObjects\PaystackTransferObject;
use App\DataTransferObjects\PaystackTransferRecipientObject;
use App\Models\Account;
use App\Models\TransactionEntry;
use App\Options\TransactionEntryStatus;
use App\Options\TransactionEntryTypes;
use Illuminate\Support\Str;

class CreateExternalTransactionEntries
{
    /**
     * @var CreatePaystackTransfer
     */
    private $paystackTransfer;

    public function __construct(CreatePaystackTransfer $paystackTransfer)
    {
        $this->paystackTransfer = $paystackTransfer;
    }

    public function execute(Account $account, array $data)
    {
        /** @var PaystackTransferRecipientObject $recipient */
        /** @var PaystackTransferObject $transfer */
        list($recipient, $transfer) = $this->paystackTransfer->execute($data);

        $debitTransactionEntry = TransactionEntry::create([
            'debit_account_id' => $account->id,
            'external_account_name' => $recipient->account_details->account_name,
            'external_account_number' => $recipient->account_details->account_number,
            'external_bank_code' => $recipient->account_details->bank_code,
            'amount' => -$data['amount'],
            'reference' => $data['reference'],
            'description' => $data['description'],
            'type' => TransactionEntryTypes::DEBIT,
            'status' => $transfer->status,
            'meta_data' => $transfer->toArray(),
        ]);

        $creditTransactionEntry = TransactionEntry::create([
            'credit_account_id' => Account::whereNumber(config('accounts.nuban_settlement_account'))->first()->id,
            'external_account_name' => $recipient->account_details->account_name,
            'external_account_number' => $recipient->account_details->account_number,
            'external_bank_code' => $recipient->account_details->bank_code,
            'amount' => $data['amount'],
            'reference' => Str::uuid()->getHex()->toString(),
            'description' => $data['description'],
            'type' => TransactionEntryTypes::CREDIT,
            'status' => $transfer->status,
            'meta_data' => $transfer->toArray(),
        ]);

        $debitTransactionEntry->credit()->sync($creditTransactionEntry->id);

        $this->updateAccountBalance($account, $debitTransactionEntry, $transfer);

        return $debitTransactionEntry;
    }

    /**
     * @param Account                $account
     * @param                        $debitTransactionEntry
     * @param PaystackTransferObject $transfer
     */
    protected function updateAccountBalance(Account $account, $debitTransactionEntry, PaystackTransferObject $transfer): void
    {
        $balance = $account->available_balance + $debitTransactionEntry->amount;

        if ($transfer->status == TransactionEntryStatus::PENDING) {
            $account->update(['available_balance' => $balance]);
        }

        if ($transfer->status == TransactionEntryStatus::SUCCESS) {
            $account->update(['available_balance' => $balance, 'book_balance' => $balance]);
        }
    }

}
