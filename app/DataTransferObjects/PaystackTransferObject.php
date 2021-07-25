<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PaystackTransferObject extends FlexibleDataTransferObject
{
    /** @var int */
    public $id;

    /** @var mixed */
    public $integration;

    /** @var mixed */
    public $recipient_id;

    /** @var string */
    public $transfer_code;

    /** @var mixed */
    public $reference;

    /** @var string */
    public $source;

    /** @var mixed */
    public $source_details;

    /** @var mixed */
    public $description;

    /** @var int */
    public $amount;

    /** @var string */
    public $currency;

    /** @var string */
    public $status;

    /** @var mixed */
    public $failures;

    /** @var mixed */
    public $titan_code;

    /** @var string */
    public $domain;

    /** @var null|\App\DataTransferObjects\PaystackTransferRecipientObject $recipient */
    public $recipient;

    /** @var \Carbon\Carbon */
    public $created_at;

    /** @var \Carbon\Carbon */
    public $updated_at;

    /** @var mixed|\Carbon\Carbon */
    public $transferred_at;

    public static function create(array $data)
    {
        return new self([
            'id' => $data['id'],
            'integration' => $data['integration'] ?? null,
            'transfer_code' => $data['transfer_code'],
            'reference' => $data['reference'] ?? null,
            'source' => $data['source'],
            'source_details' => $data['source_details'] ?? null,
            'recipient_id' => is_int($data['recipient']) ? $data['recipient'] : null,
            'description' => $data['reason'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $data['status'],
            'failures' => $data['failures'] ?? null,
            'titan_code' => $data['titan_code'] ?? null,
            'domain' => $data['domain'],
            'recipient' => ! is_int($data['recipient']) ? PaystackTransferRecipientObject::create($data['recipient']) : null,
            'created_at' => now()->parse($data['createdAt']),
            'updated_at' => now()->parse($data['updatedAt']),
            'transferred_at' => isset($data['transferred_at']) ? now()->parse($data['transferred_at']) : null,
        ]);
    }
}
