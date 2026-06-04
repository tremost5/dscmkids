<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('filter', 50);
            $table->text('message');
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->timestamps();
        });

        Schema::create('broadcast_log_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_log_id')->constrained('broadcast_logs')->cascadeOnDelete();
            $table->string('phone', 40);
            $table->string('recipient_name')->nullable();
            $table->string('status', 30);
            $table->longText('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_log_recipients');
        Schema::dropIfExists('broadcast_logs');
    }
};
