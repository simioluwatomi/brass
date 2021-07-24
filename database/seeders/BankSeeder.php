<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = json_decode(file_get_contents('tests/stubs/banks.json'), true);

        foreach ($data['banks'] as $bank) {
            Bank::create([
                'name' => $bank['name'],
                'slug' => Str::slug($bank['name']),
                'code' => $bank['code'],
            ]);
        }
    }
}
