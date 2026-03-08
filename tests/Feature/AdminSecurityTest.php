<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_is_rate_limited_after_repeated_failures(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('admin.login.submit'), [
                'email' => 'admin@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_admin_media_upload_rejects_active_file_types(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.media.create'))
            ->post(route('admin.media.store'), [
                'title' => 'Malicious Payload',
                'file' => UploadedFile::fake()->createWithContent('payload.html', '<script>alert(1)</script>'),
            ]);

        $response->assertRedirect(route('admin.media.create'));
        $response->assertSessionHasErrors('file');
    }
}
