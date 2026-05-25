<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@siterecipes.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'Admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'redactor@siterecipes.local'],
            [
                'name' => 'Redactor',
                'password' => Hash::make('password'),
                'role' => 'Redactor',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'user@siterecipes.local'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'role' => 'User',
            ]
        );
    }
}
