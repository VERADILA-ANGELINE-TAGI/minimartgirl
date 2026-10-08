<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CashierSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'kasir@minimartgirl.com'],
            [
                'name' => 'Kasir MINIMARTGIRL',
                'password' => Hash::make('kasir12345'),
            ]
        );
    }
}
