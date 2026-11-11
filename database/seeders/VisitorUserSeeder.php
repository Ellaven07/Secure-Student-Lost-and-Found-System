<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class VisitorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = config('services.visitor.password');

        if (blank($password)) {
            throw new RuntimeException('Set VISITOR_PASSWORD before creating the visitor account.');
        }

        $visitor = User::query()->firstOrNew([
            'email' => config('services.visitor.email'),
        ]);

        $visitor->forceFill([
            'name' => 'Visitor',
            'password' => $password,
            'role' => 'visitor',
            'status' => 'active',
        ])->save();
    }
}
