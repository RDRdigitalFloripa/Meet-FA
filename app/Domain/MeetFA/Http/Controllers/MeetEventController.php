<?php

namespace App\Domain\MeetFA\Http\Controllers;

use App\Domain\MeetFA\Actions\RegisterMeetEventAction;
use App\Domain\MeetFA\Models\MeetRoom;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * MeetEventController - Handles events from the frontend
 */
class MeetEventController extends Controller
{
    /**
     * Store an event from the frontend (Jitsi embed).
     */
    public function store(Request $request, string $uuid, RegisterMeetEventAction $action)
    {
        $room = MeetRoom::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'event_type' => 'required|string|max:60',
            'user_id' => 'nullable|integer',
            'participant_id' => 'nullable|string|max:120',
            'payload' => 'nullable|array',
        ]);

        // If no user_id provided, try to get from authenticated user
        if (empty($validated['user_id']) && $request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        $event = $action->execute($room, $validated, $request);

        return response()->json([
            'success' => true,
            'event_id' => $event->id,
        ]);
    }
}
