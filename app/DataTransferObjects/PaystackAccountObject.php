<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PaystackAccountObject extends FlexibleDataTransferObject
{
    /** @var int */
    public $bank_id;

    /** @var string */
    public $account_number;

    /** @var string */
    public $account_name;

    public static function create(array $data): PaystackAccountObject
    {
        return new self([
            'bank_id' => $data['bank_id'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number']
        ]);
    }
}
