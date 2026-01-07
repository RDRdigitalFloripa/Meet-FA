<?php

namespace App\Domain\MeetFA\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\MeetFA\Models\MeetRoom;
use App\Domain\MeetFA\Models\MeetChatMessage;
use App\Domain\MeetFA\Services\AiTutorService;
use Illuminate\Http\Request;

class MeetChatController extends Controller
{
    public function index($uuid)
    {
        $room = MeetRoom::where('uuid', $uuid)->firstOrFail();
        
        $messages = $room->chatMessages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'user_name' => $msg->is_ai ? 'Tutor IA 🤖' : ($msg->user?->name ?? 'Anônimo'),
                    'message' => $msg->message,
                    'is_ai' => $msg->is_ai,
                    'is_me' => $msg->user_id === auth()->id(),
                    'time' => $msg->created_at->format('H:i'),
                ];
            });

        return response()->json($messages);
    }

    public function store(Request $request, $uuid, AiTutorService $aiService)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $room = MeetRoom::where('uuid', $uuid)->firstOrFail();

        // 1. Save user message
        $userMsg = MeetChatMessage::create([
            'room_id' => $room->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_ai' => false,
        ]);

        // 2. Check for AI response
        $aiResponse = $aiService->processMessage($room, $request->message);

        if ($aiResponse) {
            MeetChatMessage::create([
                'room_id' => $room->id,
                'user_id' => null, // AI has no user ID
                'message' => $aiResponse,
                'is_ai' => true,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
