<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Transaction extends Pivot
{
    /**
     * @inheritdoc
     */
    public $incrementing = true;

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];
}
