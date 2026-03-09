<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\LearningMaterial;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_api_returns_published_news(): void
    {
        News::query()->create([
            'title' => 'Berita API',
            'slug' => 'berita-api',
            'excerpt' => 'Ringkasan',
            'body' => 'Isi lengkap',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->getJson('/api/v1/news')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Berita API']);
    }

    public function test_public_api_returns_active_announcements_and_materials(): void
    {
        Announcement::query()->create([
            'title' => 'Info API',
            'body' => 'Body',
            'is_active' => true,
        ]);

        LearningMaterial::query()->create([
            'title' => 'Materi API',
            'class_group' => '3',
            'level' => 'medium',
            'summary' => 'Summary',
            'content' => 'Content',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/announcements')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Info API']);

        $this->getJson('/api/v1/materials')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Materi API']);
    }
}
