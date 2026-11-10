<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_notifies_all_administrators(): void
    {
        $adminOne = User::factory()->create(['role' => 'admin']);
        $adminTwo = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'student']);

        app(NotificationService::class)->notifyAdministrators(
            'New report submitted',
            'A student submitted a new lost item report.',
            'info'
        );

        $this->assertDatabaseCount('notifications', 2);

        foreach ([$adminOne, $adminTwo] as $admin) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $admin->id,
                'title' => 'New report submitted',
                'message' => 'A student submitted a new lost item report.',
                'type' => 'info',
                'read_at' => null,
            ]);
        }

        $this->assertDatabaseMissing('notifications', [
            'user_id' => User::where('role', 'student')->first()->id,
            'title' => 'New report submitted',
        ]);
    }

    public function test_it_notifies_only_the_intended_user(): void
    {
        $recipient = User::factory()->create(['role' => 'student']);
        $otherStudent = User::factory()->create(['role' => 'student']);

        app(NotificationService::class)->notifyUser(
            $recipient->id,
            'Report approved',
            'Your lost item report has been approved.',
            'success'
        );

        $this->assertDatabaseCount('notifications', 1);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $recipient->id,
            'title' => 'Report approved',
            'type' => 'success',
            'read_at' => null,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $otherStudent->id,
            'title' => 'Report approved',
        ]);
    }
}