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
        Schema::create('meet_room_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('meet_rooms')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->string('role', 20)->default('student'); // student, teacher, moderator, admin
            $table->dateTime('joined_at');
            $table->dateTime('left_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('provider_participant_id', 120)->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['room_id', 'user_id']);
            $table->index('user_id');
            $table->index('joined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_room_participants');
    }
};
