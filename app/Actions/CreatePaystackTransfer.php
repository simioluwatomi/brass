<?php

namespace App\Actions;

use App\Options\PaystackOptions;
use App\Services\PaystackClient;

class CreatePaystackTransfer
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
     * @param array $data
     *
     * @throws \App\Exceptions\PaystackException
     */
    public function execute(array $data): array
    {
        $recipient = $this->client->createTransferRecipient([
            "type" => PaystackOptions::TRANSFER_RECIPIENT_TYPE,
            "name" => $data['credit_account_name'],
            "account_number" => $data['credit_account_number'],
            "bank_code" => $data['credit_bank_code'],
            "currency" => $data['currency']
        ]);

        $transfer = $this->client->transfer([
            "source" => PaystackOptions::TRANSFER_SOURCE,
            "reason" => $data['description'],
            "amount" => $data['amount'],
            "recipient" => $recipient->recipient_code,
            "reference" => $data['reference']
        ]);

        return [$recipient, $transfer];
    }

}
