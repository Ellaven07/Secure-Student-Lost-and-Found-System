<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_student_can_view_profile(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'student_no' => '2024-00001',
            'department' => 'College of Computer Studies',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.profile'));

        $response->assertOk();
        $response->assertSee('My Profile');
        $response->assertSee('2024-00001');
        $response->assertSee('College of Computer Studies');
    }

    public function test_guest_is_redirected_from_student_profile(): void
    {
        $response = $this->get(route('student.profile'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_student_profile(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('student.profile'));

        $response->assertForbidden();
    }

    public function test_student_can_update_profile_information(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'student_no' => '2024-00001',
        ]);

        $response = $this->actingAs($student)
            ->put(route('student.profile.update'), [
                'name' => 'Updated Student Name',
                'email' => 'updated.student@example.com',
                'student_no' => '2024-00002',
                'department' => 'College of Computer Studies',
                'course' => 'BS Information Technology',
                'year_level' => '4th Year',
            ]);

        $response->assertRedirect(route('student.profile'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Updated Student Name',
            'email' => 'updated.student@example.com',
            'student_no' => '2024-00002',
            'department' => 'College of Computer Studies',
            'course' => 'BS Information Technology',
            'year_level' => '4th Year',
        ]);
    }

    public function test_student_cannot_use_another_accounts_email(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
        ]);

        $anotherUser = User::factory()->create([
            'email' => 'already.used@example.com',
        ]);

        $response = $this->actingAs($student)
            ->put(route('student.profile.update'), [
                'name' => $student->name,
                'email' => $anotherUser->email,
                'student_no' => $student->student_no,
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_student_cannot_use_another_accounts_student_number(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'student_no' => '2024-00001',
        ]);

        $anotherStudent = User::factory()->create([
            'role' => 'student',
            'student_no' => '2024-00002',
        ]);

        $response = $this->actingAs($student)
            ->put(route('student.profile.update'), [
                'name' => $student->name,
                'email' => $student->email,
                'student_no' => $anotherStudent->student_no,
            ]);

        $response->assertSessionHasErrors('student_no');
    }

    public function test_student_can_upload_profile_photo(): void
    {
        Storage::fake('public');

        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student)
            ->post(route('student.profile.photo'), [
                'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
            ]);

        $response->assertRedirect(route('student.profile'));
        $response->assertSessionHas('success');

        $student->refresh();

        $this->assertNotNull($student->profile_photo_path);

        Storage::disk('public')->assertExists(
            $student->profile_photo_path
        );
    }

    public function test_student_can_change_password_with_correct_current_password(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'password' => 'password',
        ]);

        $response = $this->actingAs($student)
            ->put(route('student.profile.password'), [
                'current_password' => 'password',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertRedirect(route('student.profile'));
        $response->assertSessionHas('success');

        $student->refresh();

        $this->assertTrue(
            Hash::check('NewPassword123!', $student->password)
        );
    }

    public function test_student_cannot_change_password_with_incorrect_current_password(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'password' => 'password',
        ]);

        $response = $this->actingAs($student)
            ->put(route('student.profile.password'), [
                'current_password' => 'wrong-password',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertSessionHasErrors('current_password');

        $student->refresh();

        $this->assertTrue(
            Hash::check('password', $student->password)
        );
    }
}