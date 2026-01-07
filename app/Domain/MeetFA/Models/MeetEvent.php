<?php

namespace App\Domain\MeetFA\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MeetEvent - Auditable events log
 *
 * @property int $id
 * @property int $room_id
 * @property string $event_type
 * @property int|null $user_id
 * @property string $provider
 * @property string|null $provider_event_id
 * @property array|null $payload
 * @property \Carbon\Carbon $occurred_at
 */
class MeetEvent extends Model
{
    // Event types
    public const TYPE_JOIN = 'join';
    public const TYPE_LEAVE = 'leave';
    public const TYPE_RECORDING_STARTED = 'recording_started';
    public const TYPE_RECORDING_STOPPED = 'recording_stopped';
    public const TYPE_RECORDING_READY = 'recording_ready';
    public const TYPE_ROOM_CREATED = 'room_created';
    public const TYPE_ROOM_ENDED = 'room_ended';
    public const TYPE_ERROR = 'error';

    protected $fillable = [
        'room_id',
        'event_type',
        'user_id',
        'provider',
        'provider_event_id',
        'payload',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'occurred_at' => 'datetime',
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

    // Scopes

    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeFromProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    // Factory methods

    /**
     * Create a join event.
     */
    public static function logJoin(MeetRoom $room, int $userId, string $provider, array $payload = []): self
    {
        return static::create([
            'room_id' => $room->id,
            'event_type' => self::TYPE_JOIN,
            'user_id' => $userId,
            'provider' => $provider,
            'payload' => $payload,
            'occurred_at' => now(),
        ]);
    }

    /**
     * Create a leave event.
     */
    public static function logLeave(MeetRoom $room, int $userId, string $provider, array $payload = []): self
    {
        return static::create([
            'room_id' => $room->id,
            'event_type' => self::TYPE_LEAVE,
            'user_id' => $userId,
            'provider' => $provider,
            'payload' => $payload,
            'occurred_at' => now(),
        ]);
    }
}
