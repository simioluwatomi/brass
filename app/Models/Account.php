<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    /**
     * An account is owned by a user
     */
    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * An account has a type
     */
    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'type_id');
    }

    /**
     * An account has many debit transaction entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function debitTransactionEntries()
    {
        return $this->hasMany(TransactionEntry::class, 'debit_account_id');
    }

    /**
     * An account has many credit transaction entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function creditTransactionEntries()
    {
        return $this->hasMany(TransactionEntry::class, 'credit_account_id');
    }
}
