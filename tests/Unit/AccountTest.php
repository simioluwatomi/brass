<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountTest extends TestCase
{
    /**
     * @var Account
     */
    private $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
    }

    /** @test  */
    public function accounts_database_schema_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('accounts', [
                'id',
                'user_id',
                'type_id',
                'name',
                'number',
                'book_balance',
                'available_balance',
                'status',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function its_guarded_attribute_is_properly_set()
    {
        $this->assertEquals(['id'], $this->account->getGuarded());
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->account->owner);
    }

    /** @test */
    public function it_belongs_to_an_account_type()
    {
        $this->assertInstanceOf(AccountType::class, $this->account->type);
    }
}
