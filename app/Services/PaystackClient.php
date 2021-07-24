<?php

namespace App\Services;

use App\DataTransferObjects\BaseDTOCollection;
use App\DataTransferObjects\PaystackAccountObject;
use App\DataTransferObjects\PaystackBankObject;
use App\DataTransferObjects\PaystackTransferObject;
use App\DataTransferObjects\PaystackTransferRecipientObject;
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

    /**
     * @link https://paystack.com/docs/api/#verification-resolve-account
     *
     * @param string $account
     * @param string $bankCode
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function verifyAccount(string $account, string $bankCode): PaystackAccountObject
    {
        $response = $this->get('bank/resolve', [
            'account_number' => $account,
            'bank_code' => $bankCode,
        ]);

        $response->throw();

        return PaystackAccountObject::create($response->json(['data']));
    }

    /**
     * @link https://paystack.com/docs/api/#transfer-recipient-create
     *
     * @param array $data
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function createTransferRecipient(array $data): PaystackTransferRecipientObject
    {
        $response = $this->post('transferrecipient', $data);

        $response->throw();

        return PaystackTransferRecipientObject::create($response->json()['data']);
    }

    public function transfer(array $data)
    {
        $response = $this->post('transfer', $data);

        $response->throw();

        return PaystackTransferObject::create($response->json()['data']);
    }

    /**
     * @link https://paystack.com/docs/api/#transfer-fetch
     *
     * @param string $code
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getTransfer(string $code): PaystackTransferObject
    {
        $response = $this->get("transfer/$code");

        $response->throw();

        return PaystackTransferObject::create($response->json(['data']));
    }
}