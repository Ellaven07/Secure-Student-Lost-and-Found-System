<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrNew([
            'email' => 'admin@cvsu.edu.ph',
        ]);

        $admin->forceFill([
            'name' => 'CvSU Administrator',
            'password' => 'admin1234',
            'role' => 'admin',
            'status' => 'active',
        ])->save();
    }
}
