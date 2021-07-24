<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Options\DefaultAccountTypes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = app(DefaultAccountTypes::class)->getConstants();

        foreach ($types as $key => $value) {
            AccountType::factory()->create([
                'name' => ucfirst($value),
                'slug' => Str::slug($value),
            ]);

            $this->command->info("Account type {$value} seeded");
        }
    }
}
