<?php

namespace App\Services;

use App\DataTransferObjects\BaseDTOCollection;
use App\DataTransferObjects\PaystackAccountObject;
use App\DataTransferObjects\PaystackBankObject;
use App\DataTransferObjects\PaystackTransferObject;
use App\DataTransferObjects\PaystackTransferRecipientObject;
use App\Exceptions\PaystackException;
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
     * @throws PaystackException
     */
    public function getBanks(int $perPage = 100, string $country = 'nigeria', bool $cursor = true): \Illuminate\Support\Collection
    {
        $response = $this->get('bank', [
            'country' => $country,
            'perPage' => abs($perPage) > 100 ? 100 : abs($perPage),
            'use_cursor' => $cursor
        ]);

        if ($response->failed()) {
            throw PaystackException::create($response->json()['message']);
        }

        $banks = BaseDTOCollection::create($response->json()['data'], PaystackBankObject::class);

        return $banks->sortBy('name')->values();
    }

    /**
     * @link https://paystack.com/docs/api/#verification-resolve-account
     *
     * @param string $account
     * @param string $bankCode
     *
     * @throws PaystackException
     */
    public function verifyAccount(string $account, string $bankCode): PaystackAccountObject
    {
        $response = $this->get('bank/resolve', [
            'account_number' => $account,
            'bank_code' => $bankCode,
        ]);

        if ($response->failed()) {
            throw PaystackException::create($response->json()['message']);
        }

        return PaystackAccountObject::create($response->json(['data']));
    }

    /**
     * @link https://paystack.com/docs/api/#transfer-recipient-create
     *
     * @param array $data
     *
     * @throws PaystackException
     */
    public function createTransferRecipient(array $data): PaystackTransferRecipientObject
    {
        $response = $this->post('transferrecipient', $data);

        if ($response->failed()) {
            throw PaystackException::create($response->json()['message']);
        }

        return PaystackTransferRecipientObject::create($response->json()['data']);
    }

    /**
     * @link https://paystack.com/docs/transfers/single-transfers#initiate-a-transfer
     *
     * @param array $data
     *
     * @throws PaystackException
     */
    public function transfer(array $data): PaystackTransferObject
    {
        $response = $this->post('transfer', $data);

        if ($response->failed()) {
            throw PaystackException::create($response->json()['message']);
        }

        return PaystackTransferObject::create($response->json()['data']);
    }

    /**
     * @link https://paystack.com/docs/api/#transfer-fetch
     *
     * @param string $reference
     *
     * @throws PaystackException
     */
    public function getTransfer(string $reference): PaystackTransferObject
    {
        $response = $this->get("transfer/$reference");

        if ($response->failed()) {
            throw PaystackException::create($response->json()['message']);
        }

        return PaystackTransferObject::create($response->json(['data']));
    }
}
