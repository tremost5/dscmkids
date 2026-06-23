<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pra_companions')) {
            Schema::create('pra_companions', function (Blueprint $table) {
                $table->id();
                $table->string('companion_name');
                $table->string('whatsapp_number', 30);
                $table->boolean('attend_26_june')->default(false);
                $table->boolean('attend_27_june')->default(false);
                $table->string('payment_method', 20);
                $table->string('payment_status', 40)->default('unpaid');
                $table->string('payment_proof_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pra_companions');
    }
};
