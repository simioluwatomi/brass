<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionEntry extends Model
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'meta_data' => 'array',
    ];

    public function debit()
    {
        return $this->belongsToMany(
            TransactionEntry::class,
            'transactions',
            'credit_entry',
            'debit_entry'
        )
            ->withTimestamps()
            ->withPivot('id')
            ->using(Transaction::class);
    }

    public function credit()
    {
        return $this->belongsToMany(
            TransactionEntry::class,
            'transactions',
            'debit_entry',
            'credit_entry'
        )
            ->withTimestamps()
            ->withPivot('id')
            ->using(Transaction::class);
    }

    public function debitAccount()
    {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(Account::class, 'credit_account_id');
    }
}
