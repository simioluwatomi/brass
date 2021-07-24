<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'first_name' => 'Brass',
            'last_name' => 'Settlement',
            'email' => 'settlements@trybrass.com',
        ]);

         User::factory(10)->create();
    }
}
