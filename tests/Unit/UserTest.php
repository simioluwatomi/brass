<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test  */
    public function users_database_schema_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id',
                'first_name',
                'last_name',
                'email',
                'telephone',
                'password',
                'pin',
                'remember_token',
                'created_at',
                'updated_at',
            ])
        );
    }

    /** @test */
    public function its_guarded_attribute_is_properly_set()
    {
        $this->assertEquals(['id'], $this->user->getGuarded());
    }

    /** @test */
    public function its_hidden_attribute_is_properly_set()
    {
        $this->assertEquals(['password', 'pin', 'remember_token'], $this->user->getHidden());
    }

    /** @test */
    public function it_has_a_full_name_accessor()
    {
        $this->assertEquals($this->user->full_name, "{$this->user->first_name} {$this->user->last_name}");
    }

    /** @test */
    public function it_has_many_accounts()
    {
        $this->assertInstanceOf(Collection::class, $this->user->accounts);
    }
}
