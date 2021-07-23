<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PaystackTransferObject extends FlexibleDataTransferObject
{
    /** @var int */
    public $id;

    /** @var mixed */
    public $integration;

    /** @var string */
    public $reference;

    /** @var string */
    public $source;

    /** @var int $recipient */
    public $recipient;

    /** @var mixed */
    public $description;

    /** @var int */
    public $amount;

    /** @var string */
    public $currency;

    /** @var string */
    public $status;

    /** @var string */
    public $domain;

    /** @var \Carbon\Carbon */
    public $created_at;

    /** @var \Carbon\Carbon */
    public $updated_at;

    public static function create(array $data)
    {
        return new self([
            'id' => $data['id'],
            'integration' => $data['integration'] ?? null,
            'reference' => $data['transfer_code'],
            'source' => $data['source'],
            'recipient' => $data['recipient'],
            'description' => $data['reason'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $data['status'],
            'domain' => $data['domain'],
            'created_at' => now()->parse($data['createdAt']),
            'updated_at' => now()->parse($data['updatedAt']),
        ]);
    }
}
