<?php


namespace App\Options;


class PaystackOptions
{
    public const TRANSFER_SOURCE = 'balance';

    public const TRANSFER_RECIPIENT_TYPE = 'nuban';

    public const WEBHOOK_HEADER = 'X-Paystack-Signature';

    public const NAME = 'paystack';
}
