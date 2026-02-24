<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_quiz_banks', function (Blueprint $table) {
            $table->id();
            $table->string('day_key', 20)->unique();
            $table->string('title', 180);
            $table->string('memory_verse', 120)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('daily_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_quiz_bank_id')->constrained('daily_quiz_banks')->cascadeOnDelete();
            $table->text('question_text');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('daily_quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_quiz_question_id')->constrained('daily_quiz_questions')->cascadeOnDelete();
            $table->string('option_text', 255);
            $table->boolean('is_correct')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_quiz_options');
        Schema::dropIfExists('daily_quiz_questions');
        Schema::dropIfExists('daily_quiz_banks');
    }
};

