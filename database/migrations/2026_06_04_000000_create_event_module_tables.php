<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('location_name')->nullable();
            $table->longText('location_description')->nullable();
            $table->json('benefits')->nullable();
            $table->json('schedule')->nullable();
            $table->json('payment_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('event_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->json('class_levels')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'slug']);
        });

        Schema::create('event_banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('background_image_path')->nullable();
            $table->string('splash_title')->nullable();
            $table->string('splash_subtitle')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('event_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('event_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_path')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_group_id')->constrained('event_groups')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('nickname');
            $table->boolean('has_allergy')->default(false);
            $table->text('allergy_notes')->nullable();
            $table->date('birth_date');
            $table->string('gender', 20);
            $table->string('class_before', 20);
            $table->string('church_branch', 30);
            $table->string('parent_name');
            $table->string('whatsapp_number', 30);
            $table->text('address');
            $table->string('payment_method', 20);
            $table->string('payment_status', 40)->default('unpaid');
            $table->string('attendance_status', 30)->default('absent');
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'payment_status']);
            $table->index(['event_id', 'attendance_status']);
        });

        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('verification_status', 40)->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('event_videos');
        Schema::dropIfExists('event_galleries');
        Schema::dropIfExists('event_banners');
        Schema::dropIfExists('event_groups');
        Schema::dropIfExists('events');
    }
};
