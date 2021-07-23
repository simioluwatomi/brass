<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = AccountType::cursor();

        User::cursor()
            ->each(function ($user) use ($types) {
                $types->each(function ($type) use ($user) {
                    if ($user->email == 'settlements@trybrass.com') {
                        Account::factory()
                            ->create([
                                'user_id' => $user->id,
                                'type_id' => $type->id,
                                'name' => 'Brass Bank',
                                'number' => config('accounts.nuban_settlement_account')
                            ]);
                    } else {
                        Account::factory()
                            ->for($user, 'owner')
                            ->create([
                                'user_id' => $user->id,
                                'type_id' => $type->id,
                            ]);
                    }
                });
            });
    }
}
