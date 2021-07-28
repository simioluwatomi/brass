<?php

namespace App\Exceptions;

use Illuminate\Http\Client\HttpClientException;

class PaystackException extends HttpClientException
{
    public static function create(string $message): self
    {
        return new static($message);
    }
}
