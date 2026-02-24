<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('last_quiz_played_on')->nullable()->after('streak_days');
            $table->date('last_daily_reset_seen_on')->nullable()->after('last_quiz_played_on');
            $table->date('last_weekly_claimed_on')->nullable()->after('last_daily_reset_seen_on');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_quiz_played_on',
                'last_daily_reset_seen_on',
                'last_weekly_claimed_on',
            ]);
        });
    }
};

