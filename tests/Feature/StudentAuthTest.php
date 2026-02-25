<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_from_student_form(): void
    {
        $response = $this->post(route('student.register.submit'), [
            'name' => 'Murid Test',
            'class_group' => 'Kelas 2',
            'email' => 'murid.test@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('student.arcade'));
        $this->assertAuthenticated();

        $student = User::query()->where('email', 'murid.test@example.com')->first();
        $this->assertNotNull($student);
        $this->assertSame('student', $student->role);
        $this->assertTrue(Hash::check('secret123', $student->password));
    }

    public function test_student_can_login_with_valid_credentials(): void
    {
        $student = User::factory()->create([
            'email' => 'murid.login@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'student',
        ]);

        $response = $this->post(route('student.login.submit'), [
            'email' => 'murid.login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('student.arcade'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_admin_cannot_login_from_student_form(): void
    {
        User::factory()->create([
            'email' => 'admin.auth@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->from(route('student.login'))->post(route('student.login.submit'), [
            'email' => 'admin.auth@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('student.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
