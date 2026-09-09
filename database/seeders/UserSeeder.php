<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@pos.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
            ],
            [
                'name' => 'Owner User',
                'email' => 'owner@pos.com',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'status' => 'active',
            ],
            [
                'name' => 'Purchase User',
                'email' => 'purchase@pos.com',
                'password' => Hash::make('password123'),
                'role' => 'purchase',
                'status' => 'active',
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@pos.com',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
