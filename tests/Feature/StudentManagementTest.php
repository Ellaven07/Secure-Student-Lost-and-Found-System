<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function createStudent(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'student',
            'status' => 'active',
            'student_no' => '2024-'.fake()->unique()->numerify('#####'),
            'department' => 'College of Computer Studies',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
        ], $attributes));
    }

    public function test_admin_can_view_student_management(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent([
            'name' => 'Test Student Account',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.students.index'));

        $response->assertOk();
        $response->assertSee('Test Student Account');
        $response->assertSee($student->email);
    }

    public function test_admin_can_search_student_accounts(): void
    {
        $admin = $this->createAdmin();

        $matchingStudent = $this->createStudent([
            'name' => 'Searchable Student',
        ]);

        $this->createStudent([
            'name' => 'Different Student',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.students.index', [
                'search' => 'Searchable Student',
            ]));

        $response->assertOk();
        $response->assertSee('Searchable Student');
        $response->assertDontSee('Different Student');
    }

    public function test_admin_can_view_student_creation_form(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->get(route('admin.students.create'));

        $response->assertOk();
    }

    public function test_admin_can_create_student_account(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.students.store'), [
                'name' => 'New Test Student',
                'student_no' => '2026-10001',
                'email' => 'newstudent@example.com',
                'department' => 'College of Computer Studies',
                'course' => 'BS Information Technology',
                'year_level' => '1st Year',
                'status' => 'active',
                'password' => 'StudentPass123!',
                'password_confirmation' => 'StudentPass123!',
            ]);

        $response->assertRedirect(route('admin.students.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'New Test Student',
            'student_no' => '2026-10001',
            'email' => 'newstudent@example.com',
            'role' => 'student',
            'status' => 'active',
        ]);

        $student = User::where('email', 'newstudent@example.com')->firstOrFail();

        $this->assertTrue(
            Hash::check('StudentPass123!', $student->password)
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'title' => 'Your student account is ready',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'title' => 'New student account',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'Created student account',
        ]);
    }

    public function test_admin_can_view_student_details(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent([
            'name' => 'Student Detail Test',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.students.show', $student));

        $response->assertOk();
        $response->assertSee('Student Detail Test');
        $response->assertSee($student->email);
    }

    public function test_admin_can_view_student_edit_form(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();

        $response = $this->actingAs($admin)
            ->get(route('admin.students.edit', $student));

        $response->assertOk();
    }

    public function test_admin_can_update_student_information(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();

        $response = $this->actingAs($admin)
            ->put(route('admin.students.update', $student), [
                'name' => 'Updated Account Name',
                'student_no' => $student->student_no,
                'email' => 'updated.account@example.com',
                'department' => 'College of Engineering',
                'course' => 'BS Computer Engineering',
                'year_level' => '4th Year',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.students.show', $student));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Updated Account Name',
            'email' => 'updated.account@example.com',
            'department' => 'College of Engineering',
            'course' => 'BS Computer Engineering',
            'year_level' => '4th Year',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'title' => 'Your account information was updated',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'Updated student account',
        ]);
    }

    public function test_admin_can_deactivate_and_reactivate_student(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent([
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.students.status', $student))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'status' => 'inactive',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'title' => 'Your account status changed',
            'type' => 'warning',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.students.status', $student))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'title' => 'Your account status changed',
            'type' => 'success',
        ]);
    }

    public function test_admin_can_reset_student_password(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent([
            'password' => 'OldPassword123!',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.students.password', $student), [
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $student->refresh();

        $this->assertTrue(
            Hash::check('NewPassword123!', $student->password)
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'title' => 'Your password was reset',
            'type' => 'warning',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'Reset student password',
        ]);
    }

    public function test_admin_cannot_manage_another_admin_as_a_student(): void
    {
        $admin = $this->createAdmin();
        $anotherAdmin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('admin.students.show', $anotherAdmin))
            ->assertNotFound();

        $this->actingAs($admin)
            ->get(route('admin.students.edit', $anotherAdmin))
            ->assertNotFound();

        $this->actingAs($admin)
            ->put(route('admin.students.update', $anotherAdmin), [
                'name' => 'Changed Admin',
                'email' => $anotherAdmin->email,
                'status' => 'active',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $anotherAdmin->id,
            'role' => 'admin',
            'name' => $anotherAdmin->name,
        ]);
    }

    public function test_student_cannot_access_student_management(): void
    {
        $student = $this->createStudent();

        $this->actingAs($student)
            ->get(route('admin.students.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('admin.students.create'))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_student_management(): void
    {
        $this->get(route('admin.students.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.students.create'))
            ->assertRedirect(route('login'));
    }

    public function test_student_creation_rejects_duplicate_email_and_student_number(): void
    {
        $admin = $this->createAdmin();

        $existingStudent = $this->createStudent([
            'email' => 'existing.student@example.com',
            'student_no' => '2026-20001',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.students.create'))
            ->post(route('admin.students.store'), [
                'name' => 'Duplicate Student',
                'student_no' => $existingStudent->student_no,
                'email' => $existingStudent->email,
                'status' => 'active',
                'password' => 'StudentPass123!',
                'password_confirmation' => 'StudentPass123!',
            ]);

        $response->assertSessionHasErrors([
            'email',
            'student_no',
        ]);

        $this->assertSame(
            1,
            User::where('email', $existingStudent->email)->count()
        );
    }

    public function test_student_password_reset_requires_matching_confirmation(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent([
            'password' => 'OldPassword123!',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.students.password', $student), [
                'password' => 'NewPassword123!',
                'password_confirmation' => 'DifferentPassword123!',
            ]);

        $response->assertSessionHasErrors('password');

        $student->refresh();

        $this->assertTrue(
            Hash::check('OldPassword123!', $student->password)
        );
    }
}
