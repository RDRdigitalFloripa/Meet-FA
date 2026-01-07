<?php

namespace App\Domain\MeetFA\Policies;

use App\Domain\MeetFA\Models\MeetRoom;
use App\Domain\MeetFA\Services\JoinGuardService;
use App\Models\User;

/**
 * RoomPolicy - Authorization for room actions
 */
class RoomPolicy
{
    protected JoinGuardService $joinGuard;

    public function __construct(JoinGuardService $joinGuard)
    {
        $this->joinGuard = $joinGuard;
    }

    /**
     * Determine if user can view the rooms list.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can see rooms list
    }

    /**
     * Determine if user can view a room.
     */
    public function view(User $user, MeetRoom $room): bool
    {
        // Room creator can always view
        if ($room->created_by_user_id === $user->id) {
            return true;
        }

        // Check if user can join (same rules apply for viewing)
        $result = $this->joinGuard->canJoin($room, $user);
        return $result['allowed'];
    }

    /**
     * Determine if user can join a room.
     */
    public function join(User $user, MeetRoom $room): bool
    {
        $result = $this->joinGuard->canJoin($room, $user);
        return $result['allowed'];
    }

    /**
     * Determine if user can create rooms.
     */
    public function create(User $user): bool
    {
        // TODO: Check if user is teacher/admin
        // For MVP, all authenticated users can create rooms
        return true;
    }

    /**
     * Determine if user can manage (edit/delete) a room.
     */
    public function manage(User $user, MeetRoom $room): bool
    {
        // Only creator can manage the room
        return $room->created_by_user_id === $user->id;
    }

    /**
     * Determine if user can view room reports.
     */
    public function report(User $user, MeetRoom $room): bool
    {
        // Only creator can view reports
        return $room->created_by_user_id === $user->id;
    }

    /**
     * Determine if user can start a room.
     */
    public function start(User $user, MeetRoom $room): bool
    {
        return $this->manage($user, $room);
    }

    /**
     * Determine if user can end a room.
     */
    public function end(User $user, MeetRoom $room): bool
    {
        return $this->manage($user, $room);
    }
}
