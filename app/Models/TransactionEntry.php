<?php

namespace App\Models;

use App\Options\TransactionEntryStatus;
use App\Options\TransactionEntryTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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

    public function queryBuilderFilterFields(): array
    {
        return array_values(
            array_diff(
                Schema::getColumnListing($this->getTable()),
                ['meta_data', 'updated_at', 'created_at', 'description', 'currency']
            )
        );
    }

    public function queryBuilderSortFields(): array
    {
        return array_values(
            array_diff(Schema::getColumnListing($this->getTable()), ['meta_data', 'description', 'updated_at'])
        );
    }

    public function scopePerformedBy($query, $id)
    {
        return $query->where(function ($query) use ($id) {
            return $query->where('debit_account_id', $id)
                ->where('type', TransactionEntryTypes::DEBIT)
                ->orWhere('credit_account_id', $id)
                ->where('type', TransactionEntryTypes::CREDIT);
        });
    }

    public function scopeNotFailed($query)
    {
        return $query->where(function ($query) {
            return $query->where('status', TransactionEntryStatus::PENDING)
                ->orWhere('status', TransactionEntryStatus::SUCCESS);
        });
    }
}
