<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@diremid.com',
            'password' => Hash::make('#1984332Anvfdcs'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@diremid.com',
            'password' => Hash::make('#$asdf###'),
            'is_admin' => false,
        ]);
    }
}