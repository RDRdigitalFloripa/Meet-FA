<?php

namespace App\Domain\MeetFA\Actions;

use App\Domain\MeetFA\Models\MeetEvent;
use App\Domain\MeetFA\Models\MeetRoom;

/**
 * StartRoomAction - Starts a meeting room (sets status to live)
 */
class StartRoomAction
{
    public function execute(MeetRoom $room): MeetRoom
    {
        if ($room->isLive()) {
            return $room;
        }

        $room->update([
            'status' => MeetRoom::STATUS_LIVE,
            'starts_at' => $room->starts_at ?? now(),
        ]);

        // Log room start event
        MeetEvent::create([
            'room_id' => $room->id,
            'event_type' => 'room_started',
            'provider' => $room->provider->name,
            'payload' => ['started_at' => now()->toISOString()],
            'occurred_at' => now(),
        ]);

        return $room->fresh();
    }
}
