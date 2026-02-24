<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('student')->index()->after('email_verified_at');
            $table->string('class_group', 100)->nullable()->after('name');
            $table->unsignedInteger('points')->default(0)->after('password');
            $table->unsignedInteger('streak_days')->default(0)->after('points');
        });

        DB::table('users')
            ->where('email', 'admin@dscmkids.org')
            ->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'class_group', 'points', 'streak_days']);
        });
    }
};

