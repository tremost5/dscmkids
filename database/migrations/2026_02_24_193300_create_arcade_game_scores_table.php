<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arcade_game_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('game_key', 40);
            $table->date('played_on')->index();
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'game_key', 'played_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arcade_game_scores');
    }
};

