<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
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

    private function createCategory(array $attributes = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Electronics',
            'description' => 'Electronic devices and gadgets',
        ], $attributes));
    }

    public function test_admin_can_view_category_management(): void
    {
        $admin = $this->createAdmin();

        $category = $this->createCategory();

        $response = $this->actingAs($admin)
            ->get(route('admin.categories'));

        $response->assertOk();
        $response->assertSee('Manage Categories');
        $response->assertSee($category->name);
        $response->assertSee($category->description);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Personal Belongings',
                'description' => 'Wallets, bags, and personal items',
            ]);

        $response->assertRedirect(route('admin.categories'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Personal Belongings',
            'description' => 'Wallets, bags, and personal items',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'Created category',
        ]);
    }

    public function test_admin_can_create_category_without_description(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Documents',
                'description' => '',
            ]);

        $response->assertRedirect(route('admin.categories'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Documents',
            'description' => null,
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = $this->createAdmin();

        $category = $this->createCategory();

        $response = $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Electronic Devices',
                'description' => 'Updated category description',
            ]);

        $response->assertRedirect(route('admin.categories'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Electronic Devices',
            'description' => 'Updated category description',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'Updated category',
        ]);
    }

    public function test_category_update_allows_its_existing_name(): void
    {
        $admin = $this->createAdmin();

        $category = $this->createCategory();

        $response = $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'description' => 'Description updated',
            ]);

        $response->assertRedirect(route('admin.categories'));
        $response->assertSessionHas('success');
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = $this->createAdmin();

        $category = $this->createCategory();

        $response = $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'Deleted category',
        ]);
    }

    public function test_deleting_category_preserves_existing_item_reports(): void
    {
        $admin = $this->createAdmin();
        $student = $this->createStudent();

        $category = $this->createCategory();

        $itemId = DB::table('items')->insertGetId([
            'user_id' => $student->id,
            'category_id' => $category->id,
            'type' => 'lost',
            'item_name' => 'Test Laptop',
            'description' => 'Black laptop',
            'location' => 'Library',
            'date_lost_or_found' => now()->toDateString(),
            'status' => 'open',
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories'));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $itemId,
            'user_id' => $student->id,
            'item_name' => 'Test Laptop',
            'category_id' => null,
        ]);
    }

    public function test_category_name_is_required(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => '',
                'description' => 'Test description',
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_category_name_must_be_unique(): void
    {
        $admin = $this->createAdmin();

        $category = $this->createCategory();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => $category->name,
                'description' => 'Duplicate category',
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_category_name_cannot_exceed_100_characters(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => str_repeat('A', 101),
                'description' => 'Test description',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_category_description_cannot_exceed_2000_characters(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Valid Category',
                'description' => str_repeat('A', 2001),
            ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_student_cannot_access_category_management(): void
    {
        $student = $this->createStudent();

        $this->actingAs($student)
            ->get(route('admin.categories'))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_category_management(): void
    {
        $this->get(route('admin.categories'))
            ->assertRedirect(route('login'));
    }
}
