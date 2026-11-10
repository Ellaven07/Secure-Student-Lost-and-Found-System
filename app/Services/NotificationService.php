<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    public function notifyAdministrators(
        string $title,
        string $message,
        string $type = 'info'
    ): void {
        $adminIds = DB::table('users')
            ->where('role', 'admin')
            ->pluck('id');

        foreach ($adminIds as $adminId) {
            $this->notifyUser($adminId, $title, $message, $type);
        }
    }

    public function notifyUser(
        int|string $userId,
        string $title,
        string $message,
        string $type = 'info'
    ): void {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}