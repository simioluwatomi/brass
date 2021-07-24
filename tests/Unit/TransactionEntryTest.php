<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Bank;
use App\Models\TransactionEntry;
use Database\Seeders\BankSeeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransactionEntryTest extends TestCase
{
    /**
     * @var TransactionEntry
     */
    private $debitEntry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BankSeeder::class);

        $this->debitEntry = TransactionEntry::factory()
            ->internalDebit()
            ->create(['external_bank_code' => Bank::inRandomOrder()->first()->code]);
    }

    /** @test  */
    public function transaction_entries_database_schema_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('transaction_entries', [
                'id',
                'credit_account_id',
                'debit_account_id',
                'external_account_name',
                'external_account_number',
                'external_bank_code',
                'reference',
                'description',
                'type',
                'status',
                'currency',
                'meta_data',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function its_guarded_attribute_is_properly_set()
    {
        $this->assertEquals(['id'], $this->debitEntry->getGuarded());
    }

    /** @test */
    public function it_belongs_to_a_debit_account()
    {
        $this->assertInstanceOf(Account::class, $this->debitEntry->debitAccount);
    }

    /** @test */
    public function it_belongs_to_a_credit_account()
    {
        $this->assertInstanceOf(Account::class, $this->debitEntry->creditAccount);
    }

    /** @test */
    public function it_belongs_to_a_bank()
    {
        $this->assertInstanceOf(Account::class, $this->debitEntry->creditAccount);
    }

    /** @test */
    public function it_has_an_opposite_transaction_entry()
    {
        $this->assertInstanceOf(Collection::class, $this->debitEntry->credit);
    }
}
