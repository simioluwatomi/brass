<?php

namespace Tests\Feature;

use App\Models\AccountType;
use App\Models\User;
use App\Options\DefaultAccountTypes;
use Database\Seeders\AccountSeeder;
use Database\Seeders\AccountTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->seed(AccountTypeSeeder::class);
    }

    /** @test */
    public function guest_users_can_register_and_create_an_account()
    {
        // given there is a guest user
        // when the user makes a request to the register endpoint with the required data
        $type = AccountType::first();

        $form = [
            'account_type' => $type->id,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'business_name' => $this->faker->company,
            'email' => $this->faker->safeEmail,
            'telephone' => '+2348011223344',
            'password' => 'password',
            'pin' => '1111',
            'terms' => true,
        ];

        $this->postJson(route('api.register'), $form)
            ->assertCreated();

        $this->assertDatabaseHas('users', [
            'first_name' => $form['first_name'],
            'last_name' => $form['last_name'],
            'email' => $form['email'],
            'telephone' => $form['telephone']
        ]);

        $user = User::query()
            ->where('email', $form['email'])
            ->first();

        $this->assertTrue(Hash::check($form['password'], $user->password));

        $this->assertTrue(Hash::check($form['pin'], $user->pin));

        // account is created for that user
        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'type_id' => $type->id,
            'name' => $form['business_name'],
        ]);
    }
}
