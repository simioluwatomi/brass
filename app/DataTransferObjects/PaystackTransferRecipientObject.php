<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PaystackTransferRecipientObject extends FlexibleDataTransferObject
{
    /** @var int */
    public $id;

    /** @var mixed */
    public $integration;

    /** @var string */
    public $name;

    /** @var mixed */
    public $email;

	/** @var string */
	public $type;

	/** @var mixed */
	public $description;

    /** @var string */
    public $currency;

	/** @var mixed */
	public $domain;

	/** @var string */
	public $recipient_code;

	/** @var bool */
	public $active;

    /** @var mixed */
    public $is_deleted;

    /** @var mixed|\App\DataTransferObjects\PaystackTransferRecipientDetailsObject $details */
    public $account_details;

    /** @var mixed|\App\DataTransferObjects\PaystackTransferRecipientMetadataObject $metadata */
    public $metadata;

    /** @var \Carbon\Carbon */
    public $created_at;

    /** @var \Carbon\Carbon */
    public $updated_at;

    public static function create(array $data)
    {
        return new self([
            'id' => $data['id'],
            'integration' => $data['integration'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'currency' => $data['currency'],
            'recipient_code' => $data['recipient_code'],
            'domain' => $data['domain'] ?? null,
            'active' => $data['active'],
            'is_deleted' => $data['is_deleted'] ?? null,
            'account_details' => isset($data['details']) ? PaystackTransferRecipientDetailsObject::create($data['details']) : null,
            'metadata' => isset($data['metadata']) ? PaystackTransferRecipientMetadataObject::create($data['metadata']) : null,
            'created_at' => now()->parse($data['createdAt']),
            'updated_at' => now()->parse($data['updatedAt']),
        ]);

    }
}
