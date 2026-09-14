<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OwnerUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'owner@kyrix.com'],
            [
                'name' => 'เจ้าของร้าน KYRIX',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'status' => 'active',
            ]
        );
    }
}
