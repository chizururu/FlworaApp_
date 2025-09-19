<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mac_address')->unique();
            $table->boolean('is_online')->default(false);
            $table->boolean('ai_enabled')->default(true);
            $table->boolean('is_watering')->default(false);
            $table->dateTime('last_seen')->nullable();
            $table->foreignId('sector_id')->references('id')->on('sectors')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
