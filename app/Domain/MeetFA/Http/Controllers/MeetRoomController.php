<?php

namespace App\Domain\MeetFA\Http\Controllers;

use App\Domain\MeetFA\Actions\CreateRoomAction;
use App\Domain\MeetFA\Actions\EndRoomAction;
use App\Domain\MeetFA\Actions\StartRoomAction;
use App\Domain\MeetFA\Models\MeetRoom;
use App\Domain\MeetFA\Providers\JitsiProvider;
use App\Domain\MeetFA\Services\JoinGuardService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MeetRoomController extends Controller
{
    /**
     * Display list of rooms.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $rooms = MeetRoom::query()
            ->with(['provider', 'createdBy'])
            ->when($user, function ($query) use ($user) {
                // Show rooms created by user or rooms they can join
                $query->where('created_by_user_id', $user->id)
                    ->orWhereIn('status', [MeetRoom::STATUS_LIVE, MeetRoom::STATUS_SCHEDULED]);
            })
            ->orderBy('starts_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('meetfa.index', compact('rooms'));
    }

    /**
     * Show a specific room (join page).
     */
    public function show(Request $request, string $uuid, JoinGuardService $joinGuard)
    {
        $room = MeetRoom::where('uuid', $uuid)
            ->with(['provider', 'settings', 'createdBy'])
            ->firstOrFail();

        $user = $request->user();

        // Check if user can join
        $accessCheck = $joinGuard->canJoin($room, $user);

        if (!$accessCheck['allowed']) {
            return view('meetfa.access-denied', [
                'room' => $room,
                'reason' => $accessCheck['reason'],
            ]);
        }

        // Get Jitsi configuration
        $jitsiProvider = new JitsiProvider($room->provider);
        $jitsiConfig = $jitsiProvider->getJoinConfig($room, $user);

        return view('meetfa.room', [
            'room' => $room,
            'jitsiConfig' => $jitsiConfig,
            'user' => $user,
        ]);
    }

    /**
     * Show room report.
     */
    public function report(Request $request, string $uuid)
    {
        $room = MeetRoom::where('uuid', $uuid)
            ->with(['participants.user', 'events', 'recordings'])
            ->firstOrFail();

        // Check permission
        if ($room->created_by_user_id !== $request->user()?->id) {
            abort(403, 'Você não tem permissão para ver este relatório.');
        }

        // Calculate participation stats
        $participants = $room->participants()
            ->select('user_id')
            ->selectRaw('SUM(duration_seconds) as total_seconds')
            ->selectRaw('MIN(joined_at) as first_join')
            ->selectRaw('MAX(left_at) as last_leave')
            ->groupBy('user_id')
            ->with('user')
            ->get();

        return view('meetfa.report', [
            'room' => $room,
            'participants' => $participants,
        ]);
    }

    /**
     * Create a new room (form page).
     */
    public function create()
    {
        return view('meetfa.create');
    }

    /**
     * Store a new room.
     */
    public function store(Request $request, CreateRoomAction $action)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'min_presence_minutes' => 'nullable|integer|min:0',
            'min_presence_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $room = $action->execute($validated, $request->user());

        return redirect()->route('meet.show', $room->uuid)
            ->with('success', 'Sala criada com sucesso!');
    }

    /**
     * Start a room (AJAX).
     */
    public function start(Request $request, string $uuid, StartRoomAction $action)
    {
        $room = MeetRoom::where('uuid', $uuid)->firstOrFail();

        // Check permission
        if ($room->created_by_user_id !== $request->user()?->id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $room = $action->execute($room);

        return response()->json([
            'success' => true,
            'status' => $room->status,
        ]);
    }

    /**
     * End a room (AJAX).
     */
    public function end(Request $request, string $uuid, EndRoomAction $action)
    {
        $room = MeetRoom::where('uuid', $uuid)->firstOrFail();

        // Check permission
        if ($room->created_by_user_id !== $request->user()?->id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $room = $action->execute($room);

        return response()->json([
            'success' => true,
            'status' => $room->status,
        ]);
    }
}
