<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meet_room_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->unique()->constrained('meet_rooms')->cascadeOnDelete();
            $table->boolean('enable_lobby')->default(false);
            $table->boolean('mute_on_start')->default(true);
            $table->boolean('camera_off_on_start')->default(false);
            $table->boolean('allow_recording')->default(true);
            $table->integer('max_participants')->nullable();
            $table->json('settings')->nullable(); // Extra provider-specific settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_room_settings');
    }
};
