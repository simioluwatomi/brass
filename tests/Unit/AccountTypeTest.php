<?php

namespace Tests\Unit;

use App\Models\AccountType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountTypeTest extends TestCase
{
    /**
     * @var AccountType
     */
    private $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = AccountType::factory()->create();
    }

    /** @test  */
    public function account_types_database_schema_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('account_types', [
                'id',
                'name',
                'slug',
                'currency',
                'interest_rate',
                'monthly_maintenance_charge',
                'minimum_balance',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function its_guarded_attribute_is_properly_set()
    {
        $this->assertEquals(['id'], $this->type->getGuarded());
    }

    /** @test */
    public function it_has_many_accounts()
    {
        $this->assertInstanceOf(Collection::class, $this->type->accounts);
    }
}
