<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@promobile.com'],
            [
                'name'     => 'Admin',
                'surname'  => 'ProMobile',
                'email'    => 'admin@promobile.com',
                'password' => Hash::make('Admin@1234'),
                'role'     => 'admin',
                'is_admin' => true,
            ]
        );
    }
}