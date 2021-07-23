<?php


namespace App\DataTransferObjects;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PaystackBankObject extends FlexibleDataTransferObject
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $slug;

    /** @var string */
    public $code;

    /** @var mixed */
    public $long_code;

    /** @var mixed */
    public $gateway;

    /** @var bool */
    public $pay_with_bank;

    /** @var bool */
    public $active;

    /** @var mixed */
    public $is_deleted;

    /** @var string */
    public $country;

    /** @var string */
    public $currency;

    /** @var string */
    public $type;

    /** @var \Carbon\Carbon */
    public $created_at;

    /** @var \Carbon\Carbon */
    public $updated_at;

    public static function create(array $data = [])
    {
        return new self([
            'id' => $data['id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'code' => $data['code'],
            'long_code' => empty($data['longcode']) ? null : $data['longcode'],
            'gateway' => $data['gateway'],
            'pay_with_bank' => $data['pay_with_bank'],
            'active' => $data['active'],
            'is_deleted' => $data['is_deleted'],
            'country' => $data['country'],
            'currency' => $data['currency'],
            'type' => $data['type'],
            'created_at' => now()->parse($data['createdAt']),
            'updated_at' => now()->parse($data['updatedAt']),
        ]);
    }

}
