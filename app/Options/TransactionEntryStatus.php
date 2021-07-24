<?php

namespace App\Options;

use App\Traits\ClassConstant;

class TransactionEntryStatus
{
    use ClassConstant;

    public const PENDING = 'pending';

    public const FAILED = 'failed';

    public const REVERSED = 'reversed';

    public const SUCCESS = 'success';
}
