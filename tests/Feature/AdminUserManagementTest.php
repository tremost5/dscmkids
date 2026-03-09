<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user_from_management_module(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Editor Baru',
                'email' => 'editor.baru@example.com',
                'class_group' => '',
                'role' => 'editor',
                'password' => 'secret1234',
                'password_confirmation' => 'secret1234',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'editor.baru@example.com',
            'role' => 'editor',
            'is_active' => true,
        ]);
    }

    public function test_editor_cannot_access_user_management_module(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $this->actingAs($editor)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_bulk_deactivate_users_except_self(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.users.bulk'), [
                'action' => 'deactivate',
                'user_ids' => [$admin->id, $student->id],
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'is_active' => false,
        ]);
    }
}
