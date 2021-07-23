<?php

namespace App\Services;

use App\DataTransferObjects\BaseDTOCollection;
use App\DataTransferObjects\PaystackBankObject;
use Illuminate\Http\Client\PendingRequest;

class PaystackClient extends PendingRequest
{
    /**
     * @link https://paystack.com/docs/api/#miscellaneous-bank
     *
     * @param int    $perPage
     * @param string $country
     * @param bool   $cursor
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getBanks(int $perPage = 100, string $country = 'nigeria', bool $cursor = true): \Illuminate\Support\Collection
    {
        $response = $this->get('bank', [
            'country' => $country,
            'perPage' => abs($perPage) > 100 ? 100 : abs($perPage),
            'use_cursor' => $cursor
        ]);

        $response->throw();

        $banks = BaseDTOCollection::create($response->json()['data'], PaystackBankObject::class);

        return $banks->sortBy('name')->values();
    }
}
