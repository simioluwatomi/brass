<?php

namespace Tests\Unit;

use App\Models\Bank;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BankTest extends TestCase
{
    /**
     * @var Bank
     */
    private $bank;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bank = Bank::factory()->create();
    }

    /** @test  */
    public function banks_database_schema_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('banks', [
                'id',
                'name',
                'slug',
                'code',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function its_guarded_attribute_is_properly_set()
    {
        $this->assertEquals(['id'], $this->bank->getGuarded());
    }

    /** @test */
    public function it_has_many_transaction_entries()
    {
        $this->assertInstanceOf(Collection::class, $this->bank->transactionEntries);
    }
}
