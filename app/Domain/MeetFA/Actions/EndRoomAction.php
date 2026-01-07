<?php

namespace App\Domain\MeetFA\Actions;

use App\Domain\MeetFA\Models\MeetEvent;
use App\Domain\MeetFA\Models\MeetRoom;
use App\Domain\MeetFA\Models\MeetRoomParticipant;

/**
 * EndRoomAction - Ends a meeting room
 */
class EndRoomAction
{
    public function execute(MeetRoom $room): MeetRoom
    {
        if ($room->isEnded()) {
            return $room;
        }

        // Mark all connected participants as left
        $room->participants()
            ->whereNull('left_at')
            ->each(function (MeetRoomParticipant $participant) {
                $participant->markAsLeft();
            });

        $room->update([
            'status' => MeetRoom::STATUS_ENDED,
            'ends_at' => now(),
        ]);

        // Log room end event
        MeetEvent::create([
            'room_id' => $room->id,
            'event_type' => MeetEvent::TYPE_ROOM_ENDED,
            'provider' => $room->provider->name,
            'payload' => [
                'ended_at' => now()->toISOString(),
                'total_participants' => $room->participants()->distinct('user_id')->count(),
            ],
            'occurred_at' => now(),
        ]);

        return $room->fresh();
    }
}
