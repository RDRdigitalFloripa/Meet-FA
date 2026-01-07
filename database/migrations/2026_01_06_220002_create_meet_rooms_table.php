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
        Schema::create('meet_rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('provider_id')->constrained('meet_providers')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('room_name', 180);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('status', 30)->default('draft'); // draft, scheduled, live, ended, canceled
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Academic integration (SGA/Moodle)
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('discipline_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();

            // Presence policies
            $table->integer('min_presence_minutes')->default(0);
            $table->integer('min_presence_percent')->default(0);
            $table->boolean('requires_financial_ok')->default(true);
            $table->boolean('requires_enrollment')->default(true);

            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['provider_id', 'status']);
            $table->index(['course_id', 'class_id', 'discipline_id']);
            $table->index('starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_rooms');
    }
};
