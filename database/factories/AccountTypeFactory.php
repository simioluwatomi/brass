<?php

namespace Database\Factories;

use App\Models\AccountType;
use App\Options\DefaultAccountTypes;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AccountTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $items = app(DefaultAccountTypes::class)->getConstants();

        return [
            'name' => ucfirst($items[array_rand($items)]),
            'slug' => function (array $attributes) {
                return Str::slug($attributes['name']);
            },
            'currency' => 'NGN',
        ];
    }
}
