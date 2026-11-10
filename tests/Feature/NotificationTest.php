<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function createStudent(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    private function createNotification(
        int $userId,
        string $title = 'Test Notification',
        ?string $readAt = null
    ): string {
        $id = (string) Str::uuid();

        DB::table('notifications')->insert([
            'id' => $id,
            'user_id' => $userId,
            'title' => $title,
            'message' => 'This is a test notification.',
            'type' => 'info',
            'read_at' => $readAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    public function test_student_can_view_own_notifications(): void
    {
        $student = $this->createStudent();

        $this->createNotification(
            $student->id,
            'Student Test Notification'
        );

        $response = $this->actingAs($student)
            ->get(route('student.notifications'));

        $response->assertOk();
        $response->assertSee('Student Test Notification');
        $response->assertViewHas('totalNotifications', 1);
        $response->assertViewHas('unreadNotifications', 1);
        $response->assertViewHas('readNotifications', 0);
        $response->assertViewHas('isAdmin', false);
    }

    public function test_admin_can_view_own_notifications(): void
    {
        $admin = $this->createAdmin();

        $this->createNotification(
            $admin->id,
            'Admin Test Notification'
        );

        $response = $this->actingAs($admin)
            ->get(route('admin.notifications'));

        $response->assertOk();
        $response->assertSee('Admin Test Notification');
        $response->assertViewHas('totalNotifications', 1);
        $response->assertViewHas('isAdmin', true);
    }

    public function test_user_cannot_view_another_users_notifications(): void
    {
        $student = $this->createStudent();
        $anotherStudent = $this->createStudent();

        $this->createNotification(
            $anotherStudent->id,
            'Private Notification'
        );

        $response = $this->actingAs($student)
            ->get(route('student.notifications'));

        $response->assertOk();
        $response->assertDontSee('Private Notification');
        $response->assertViewHas('totalNotifications', 0);
    }

    public function test_student_can_mark_own_notification_as_read(): void
    {
        $student = $this->createStudent();

        $notificationId = $this->createNotification(
            $student->id
        );

        $response = $this->actingAs($student)
            ->from(route('student.notifications'))
            ->patch(route('notifications.read', $notificationId));

        $response->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'id' => $notificationId,
            'user_id' => $student->id,
        ]);

        $this->assertNotNull(
            DB::table('notifications')
                ->where('id', $notificationId)
                ->value('read_at')
        );
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $student = $this->createStudent();
        $anotherStudent = $this->createStudent();

        $notificationId = $this->createNotification(
            $anotherStudent->id
        );

        $this->actingAs($student)
            ->patch(route('notifications.read', $notificationId));

        $this->assertNull(
            DB::table('notifications')
                ->where('id', $notificationId)
                ->value('read_at')
        );
    }

    public function test_user_can_mark_all_own_notifications_as_read(): void
    {
        $student = $this->createStudent();
        $anotherStudent = $this->createStudent();

        $first = $this->createNotification($student->id, 'First');
        $second = $this->createNotification($student->id, 'Second');
        $other = $this->createNotification($anotherStudent->id, 'Other');

        $response = $this->actingAs($student)
            ->from(route('student.notifications'))
            ->patch(route('notifications.read-all'));

        $response->assertRedirect();

        $this->assertNotNull(
            DB::table('notifications')->where('id', $first)->value('read_at')
        );

        $this->assertNotNull(
            DB::table('notifications')->where('id', $second)->value('read_at')
        );

        $this->assertNull(
            DB::table('notifications')->where('id', $other)->value('read_at')
        );
    }

    public function test_user_can_clear_own_notifications_only(): void
    {
        $student = $this->createStudent();
        $anotherStudent = $this->createStudent();

        $ownNotification = $this->createNotification($student->id);
        $otherNotification = $this->createNotification($anotherStudent->id);

        $response = $this->actingAs($student)
            ->from(route('student.notifications'))
            ->delete(route('notifications.clear'));

        $response->assertRedirect();

        $this->assertDatabaseMissing('notifications', [
            'id' => $ownNotification,
        ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $otherNotification,
            'user_id' => $anotherStudent->id,
        ]);
    }

    public function test_guest_cannot_access_student_notifications(): void
    {
        $this->get(route('student.notifications'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_notification_actions(): void
    {
        $this->patch(route('notifications.read-all'))
            ->assertRedirect(route('login'));

        $this->delete(route('notifications.clear'))
            ->assertRedirect(route('login'));
    }
}
