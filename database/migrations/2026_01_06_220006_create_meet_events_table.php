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
        Schema::create('meet_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('meet_rooms')->cascadeOnDelete();
            $table->string('event_type', 60); // join, leave, recording_started, recording_ready, room_created, room_ended, error
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('provider', 50); // jitsi, livekit
            $table->string('provider_event_id', 120)->nullable();
            $table->json('payload')->nullable();
            $table->dateTime('occurred_at');
            $table->timestamps();

            // Indexes
            $table->index(['room_id', 'event_type']);
            $table->index('user_id');
            $table->index('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_events');
    }
};
