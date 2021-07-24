<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\TransactionEntry;
use App\Options\TransactionEntryStatus;
use App\Options\TransactionEntryTypes;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'reference'  => Str::uuid()->getHex()->toString(),
            'status'      => $this->faker->randomElement(app(TransactionEntryStatus::class)->getConstants()),
            'description' => $this->faker->text(150),
        ];
    }

    /**
     * Indicate that the transaction entry is a debit transaction
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function externalDebit()
    {
        return $this->state(function () {
            return [
                'debit_account_id'      => Account::factory(),
                'external_account_name'   => $this->faker->name,
                'external_account_number' => $this->faker->unique()->regexify('[0-9]{10}'),
                'external_bank_code'      => $this->faker->unique()->regexify('[0-9]{3}'),
                'type' => TransactionEntryTypes::DEBIT,
                'amount' => $this->faker->numberBetween(-100000, -1500000)
            ];
        });
    }

    /**
     * Indicate that the transaction entry is a debit transaction
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function externalCredit()
    {
        return $this->state(function () {
            return [
                'credit_account_id'      => Account::factory(),
                'external_account_name'   => $this->faker->name,
                'external_account_number' => $this->faker->unique()->regexify('[0-9]{10}'),
                'external_bank_code'      => $this->faker->unique()->regexify('[0-9]{3}'),
                'type' => TransactionEntryTypes::CREDIT,
                'amount' => $this->faker->numberBetween(100000, 1500000)
            ];
        });
    }

    /**
     * Indicate that the transaction entry is a debit transaction
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function internalDebit()
    {
        return $this->state(function () {
            return [
                'credit_account_id'      => Account::factory(),
                'debit_account_id'      => Account::factory(),
                'type' => TransactionEntryTypes::DEBIT,
                'amount' => $this->faker->numberBetween(-100000, -1500000)
            ];
        });
    }

    /**
     * Indicate that the transaction entry is a debit transaction
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function internalCredit()
    {
        return $this->state(function () {
            return [
                'credit_account_id'      => Account::factory(),
                'debit_account_id'      => Account::factory(),
                'type' => TransactionEntryTypes::CREDIT,
                'amount' => $this->faker->numberBetween(100000, 1500000)
            ];
        });
    }
}
