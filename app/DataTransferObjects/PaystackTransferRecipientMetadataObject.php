<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PaystackTransferRecipientMetadataObject extends FlexibleDataTransferObject
{
	/** @var mixed */
	public $job;

	public static function create(array $data)
    {
        return new self([
            'job' => $data['job'] ?? null,
        ]);
    }
}
