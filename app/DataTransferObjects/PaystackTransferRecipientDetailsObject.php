<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PaystackTransferRecipientDetailsObject extends FlexibleDataTransferObject
{
	/** @var mixed */
	public $account_number;

	/** @var mixed */
	public $account_name;

	/** @var mixed */
	public $bank_code;

	/** @var mixed */
	public $bank_name;

	/** @var mixed */
	public $authorization_code;


	public static function create(array $data)
    {
        return new self([
            'account_number' => $data['account_number'] ?? null,
            'account_name' => $data['account_name'] ?? null,
            'bank_code' => $data['bank_code'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'authorization_code' => $data['authorization_code'] ?? null,
        ]);
    }
}
