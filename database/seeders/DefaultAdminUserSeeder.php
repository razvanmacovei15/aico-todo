<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'support@aico.swiss';

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make('password'),
            ]
        );
    }
}
