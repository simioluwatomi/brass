<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;
use App\Options\AccountStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type_id' => AccountType::factory(),
            'number' => $this->faker->unique()->regexify('[0-9]{10}'),
            'book_balance' => $this->faker->numberBetween(1000000, 20000000),
            'available_balance' => $this->faker->numberBetween(1000000, 20000000),
            'name' => $this->faker->name,
            'status' => AccountStatus::ACTIVE,
        ];
    }
}
