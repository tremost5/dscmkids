<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_galleries', function (Blueprint $table) {
            if (!Schema::hasColumn('event_galleries', 'group_slug')) {
                $table->string('group_slug', 30)->nullable()->after('event_id')->index();
            }
        });

        DB::table('event_galleries')
            ->whereNull('group_slug')
            ->update(['group_slug' => 'grup-1']);
    }

    public function down(): void
    {
        Schema::table('event_galleries', function (Blueprint $table) {
            if (Schema::hasColumn('event_galleries', 'group_slug')) {
                $table->dropColumn('group_slug');
            }
        });
    }
};
