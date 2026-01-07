<?php

namespace App\Domain\MeetFA\Actions;

use App\Domain\MeetFA\Models\MeetEvent;
use App\Domain\MeetFA\Models\MeetRoom;
use App\Domain\MeetFA\Models\MeetRoomParticipant;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * RegisterMeetEventAction - Registers events from the video provider
 */
class RegisterMeetEventAction
{
    public function execute(MeetRoom $room, array $data, ?Request $request = null): MeetEvent
    {
        $eventType = $data['event_type'] ?? $data['eventType'] ?? 'unknown';
        $userId = $data['user_id'] ?? null;
        $provider = $data['provider'] ?? $room->provider->name;

        // Create the event record
        $event = MeetEvent::create([
            'room_id' => $room->id,
            'event_type' => $eventType,
            'user_id' => $userId,
            'provider' => $provider,
            'provider_event_id' => $data['provider_event_id'] ?? null,
            'payload' => $data,
            'occurred_at' => isset($data['occurred_at']) ? parse($data['occurred_at']) : now(),
        ]);

        // Handle specific events
        match ($eventType) {
            MeetEvent::TYPE_JOIN, 'join', 'participantJoined' => $this->handleJoin($room, $userId, $request, $data),
            MeetEvent::TYPE_LEAVE, 'leave', 'participantLeft' => $this->handleLeave($room, $userId, $data),
            default => null,
        };

        return $event;
    }

    protected function handleJoin(MeetRoom $room, ?int $userId, ?Request $request, array $data): void
    {
        if (!$userId) {
            return;
        }

        // Determine role
        $user = User::find($userId);
        $role = $data['role'] ?? MeetRoomParticipant::ROLE_STUDENT;

        // Check if user is the room creator (teacher)
        if ($room->created_by_user_id === $userId) {
            $role = MeetRoomParticipant::ROLE_TEACHER;
        }

        // Create participation record
        MeetRoomParticipant::create([
            'room_id' => $room->id,
            'user_id' => $userId,
            'role' => $role,
            'joined_at' => now(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'provider_participant_id' => $data['participant_id'] ?? null,
            'provider_payload' => $data,
        ]);
    }

    protected function handleLeave(MeetRoom $room, ?int $userId, array $data): void
    {
        if (!$userId) {
            return;
        }

        // Find the latest open participation for this user
        $participant = $room->participants()
            ->where('user_id', $userId)
            ->whereNull('left_at')
            ->latest('joined_at')
            ->first();

        if ($participant) {
            $participant->markAsLeft();
        }
    }
}
