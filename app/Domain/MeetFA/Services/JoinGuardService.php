<?php

namespace App\Domain\MeetFA\Services;

use App\Domain\MeetFA\Models\MeetRoom;
use App\Models\User;

/**
 * JoinGuardService - Validates if a user can join a room
 *
 * Implements academic access rules (enrollment, financial status, etc.)
 */
class JoinGuardService
{
    /**
     * Check if user can join the room.
     *
     * @param MeetRoom $room
     * @param User $user
     * @return array ['allowed' => bool, 'reason' => string|null]
     */
    public function canJoin(MeetRoom $room, User $user): array
    {
        // Check if room is joinable
        if (!$room->canJoin()) {
            return [
                'allowed' => false,
                'reason' => 'A sala não está disponível para acesso no momento.',
            ];
        }

        // Teachers and admins can always join
        if ($this->isTeacherOrAdmin($room, $user)) {
            return ['allowed' => true, 'reason' => null];
        }

        // Check enrollment requirement
        if ($room->requires_enrollment && !$this->checkEnrollment($room, $user)) {
            return [
                'allowed' => false,
                'reason' => 'Você não está matriculado nesta disciplina.',
            ];
        }

        // Check financial status requirement
        if ($room->requires_financial_ok && !$this->checkFinancialStatus($user)) {
            return [
                'allowed' => false,
                'reason' => 'Sua situação financeira está irregular.',
            ];
        }

        // Check time window (if scheduled)
        if (!$this->checkTimeWindow($room)) {
            return [
                'allowed' => false,
                'reason' => 'A aula ainda não começou ou já terminou.',
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Check if user is teacher or admin.
     */
    protected function isTeacherOrAdmin(MeetRoom $room, User $user): bool
    {
        // Room creator is considered teacher
        if ($room->created_by_user_id === $user->id) {
            return true;
        }

        // TODO: Check user role from SGA integration
        // For now, check if user has 'admin' or 'teacher' attribute
        return false;
    }

    /**
     * Check if user is enrolled in the course/class.
     *
     * TODO: Integrate with SGA to verify enrollment
     */
    protected function checkEnrollment(MeetRoom $room, User $user): bool
    {
        // Placeholder - should integrate with SGA
        // For MVP, we allow access if no course/class is set
        if (!$room->course_id && !$room->class_id && !$room->discipline_id) {
            return true;
        }

        // TODO: Call SGA API to verify enrollment
        return true;
    }

    /**
     * Check if user's financial status is regular.
     *
     * TODO: Integrate with SGA to verify financial status
     */
    protected function checkFinancialStatus(User $user): bool
    {
        // Placeholder - should integrate with SGA
        // TODO: Call SGA API to verify financial status
        return true;
    }

    /**
     * Check if current time is within the scheduled window.
     */
    protected function checkTimeWindow(MeetRoom $room): bool
    {
        // If room is already live, allow access
        if ($room->isLive()) {
            return true;
        }

        $now = now();

        // If no schedule, allow access
        if (!$room->starts_at) {
            return true;
        }

        // Allow joining 5 minutes before start
        $windowStart = $room->starts_at->subMinutes(5);

        // If no end time, allow access after start
        if (!$room->ends_at) {
            return $now->gte($windowStart);
        }

        return $now->between($windowStart, $room->ends_at);
    }
}
