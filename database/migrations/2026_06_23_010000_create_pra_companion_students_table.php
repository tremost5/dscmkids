<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pra_companion_students')) {
            Schema::create('pra_companion_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pra_companion_id')->constrained('pra_companions')->cascadeOnDelete();
                $table->foreignId('event_registration_id')->constrained('event_registrations')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['pra_companion_id', 'event_registration_id'], 'pra_companion_student_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pra_companion_students');
    }
};
