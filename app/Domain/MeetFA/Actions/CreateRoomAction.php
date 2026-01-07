<?php

namespace App\Domain\MeetFA\Actions;

use App\Domain\MeetFA\Models\MeetEvent;
use App\Domain\MeetFA\Models\MeetProvider;
use App\Domain\MeetFA\Models\MeetRoom;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * CreateRoomAction - Creates a new meeting room
 */
class CreateRoomAction
{
    public function execute(array $data, ?User $createdBy = null): MeetRoom
    {
        // Get provider (default to Jitsi)
        $provider = isset($data['provider_id'])
            ? MeetProvider::findOrFail($data['provider_id'])
            : MeetProvider::jitsi();

        if (!$provider) {
            throw new \RuntimeException('No active video provider found');
        }

        // Create the room
        $room = MeetRoom::create([
            'uuid' => Str::uuid()->toString(),
            'provider_id' => $provider->id,
            'title' => $data['title'],
            'room_name' => $data['room_name'] ?? Str::slug($data['title']) . '-' . Str::random(8),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'status' => $data['status'] ?? MeetRoom::STATUS_SCHEDULED,
            'created_by_user_id' => $createdBy?->id,
            'course_id' => $data['course_id'] ?? null,
            'class_id' => $data['class_id'] ?? null,
            'discipline_id' => $data['discipline_id'] ?? null,
            'lesson_id' => $data['lesson_id'] ?? null,
            'min_presence_minutes' => $data['min_presence_minutes'] ?? 0,
            'min_presence_percent' => $data['min_presence_percent'] ?? 0,
            'requires_financial_ok' => $data['requires_financial_ok'] ?? true,
            'requires_enrollment' => $data['requires_enrollment'] ?? true,
            'metadata' => $data['metadata'] ?? null,
        ]);

        // Log room creation event
        MeetEvent::create([
            'room_id' => $room->id,
            'event_type' => MeetEvent::TYPE_ROOM_CREATED,
            'user_id' => $createdBy?->id,
            'provider' => $provider->name,
            'payload' => ['title' => $room->title],
            'occurred_at' => now(),
        ]);

        return $room;
    }
}
