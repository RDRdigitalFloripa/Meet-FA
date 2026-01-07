<?php

namespace App\Domain\MeetFA\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MeetRoomParticipant - Individual participation tracking
 *
 * @property int $id
 * @property int $room_id
 * @property int $user_id
 * @property string $role
 * @property \Carbon\Carbon $joined_at
 * @property \Carbon\Carbon|null $left_at
 * @property int $duration_seconds
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $provider_participant_id
 * @property array|null $provider_payload
 */
class MeetRoomParticipant extends Model
{
    public const ROLE_STUDENT = 'student';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'room_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
        'duration_seconds',
        'ip_address',
        'user_agent',
        'provider_participant_id',
        'provider_payload',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
            'provider_payload' => 'array',
        ];
    }

    // Relationships

    public function room(): BelongsTo
    {
        return $this->belongsTo(MeetRoom::class, 'room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helpers

    /**
     * Check if participant is still connected.
     */
    public function isConnected(): bool
    {
        return $this->left_at === null;
    }

    /**
     * Mark participant as left and calculate duration.
     */
    public function markAsLeft(): void
    {
        $this->left_at = now();
        $this->duration_seconds = $this->joined_at->diffInSeconds($this->left_at);
        $this->save();
    }

    /**
     * Get total participation time for a user in a room.
     */
    public static function totalSecondsForUser(int $roomId, int $userId): int
    {
        return static::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->sum('duration_seconds');
    }
}
