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
        Schema::create('meet_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('meet_rooms')->cascadeOnDelete();
            $table->string('provider_recording_id', 120)->nullable();
            $table->string('status', 30)->default('queued'); // queued, recording, processing, ready, failed
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->integer('duration_seconds')->default(0);

            // Storage destination
            $table->string('storage_disk', 50)->nullable(); // local, s3, r2, minio, vimeo
            $table->string('storage_path', 255)->nullable();
            $table->string('public_url', 500)->nullable();
            $table->string('checksum', 128)->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['room_id', 'status']);
            $table->index('provider_recording_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_recordings');
    }
};
