<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    /**
     * A bank has many transaction entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactionEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransactionEntry::class, 'external_bank_code', 'code');
    }
}
