<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_broadcasts', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('channel');
            $table->unsignedInteger('processed_count')->default(0)->after('target_count');
            $table->unsignedInteger('failed_count')->default(0)->after('processed_count');
            $table->text('last_error')->nullable()->after('failed_count');
            $table->index(['status', 'sent_at'], 'notification_broadcasts_status_sent_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('notification_broadcasts', function (Blueprint $table) {
            $table->dropIndex('notification_broadcasts_status_sent_at_index');
            $table->dropColumn([
                'status',
                'processed_count',
                'failed_count',
                'last_error',
            ]);
        });
    }
};
