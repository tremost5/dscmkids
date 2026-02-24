<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('quiz_date')->index();
            $table->string('quiz_key', 40);
            $table->unsignedSmallInteger('score')->default(0);
            $table->unsignedTinyInteger('correct_answers')->default(0);
            $table->unsignedTinyInteger('total_questions')->default(0);
            $table->string('badge_awarded', 80)->nullable();
            $table->json('answers')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'quiz_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_quiz_results');
    }
};

