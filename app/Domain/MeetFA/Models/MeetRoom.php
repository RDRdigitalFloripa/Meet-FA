<?php

namespace App\Domain\MeetFA\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * MeetRoom - A live class session
 *
 * @property int $id
 * @property string $uuid
 * @property int $provider_id
 * @property string $title
 * @property string $room_name
 * @property \Carbon\Carbon|null $starts_at
 * @property \Carbon\Carbon|null $ends_at
 * @property string $status
 * @property int|null $created_by_user_id
 * @property int|null $course_id
 * @property int|null $class_id
 * @property int|null $discipline_id
 * @property int|null $lesson_id
 * @property int $min_presence_minutes
 * @property int $min_presence_percent
 * @property bool $requires_financial_ok
 * @property bool $requires_enrollment
 * @property array|null $metadata
 */
class MeetRoom extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_LIVE = 'live';
    public const STATUS_ENDED = 'ended';
    public const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'uuid',
        'provider_id',
        'title',
        'room_name',
        'starts_at',
        'ends_at',
        'status',
        'created_by_user_id',
        'course_id',
        'class_id',
        'discipline_id',
        'lesson_id',
        'min_presence_minutes',
        'min_presence_percent',
        'requires_financial_ok',
        'requires_enrollment',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'requires_financial_ok' => 'boolean',
            'requires_enrollment' => 'boolean',
            'metadata' => 'array',
        ];
    }

    /**
     * Boot the model and auto-generate UUID.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            if (empty($room->uuid)) {
                $room->uuid = (string) Str::uuid();
            }
            if (empty($room->room_name)) {
                $room->room_name = Str::slug($room->title) . '-' . Str::random(8);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships

    public function provider(): BelongsTo
    {
        return $this->belongsTo(MeetProvider::class, 'provider_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function settings(): HasOne
    {
        return $this->hasOne(MeetRoomSetting::class, 'room_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(MeetRoomParticipant::class, 'room_id');
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(MeetRecording::class, 'room_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MeetEvent::class, 'room_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(MeetChatMessage::class, 'room_id');
    }

    // Scopes

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeLive($query)
    {
        return $query->where('status', self::STATUS_LIVE);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    // Helpers

    public function isLive(): bool
    {
        return $this->status === self::STATUS_LIVE;
    }

    public function isEnded(): bool
    {
        return $this->status === self::STATUS_ENDED;
    }

    public function canJoin(): bool
    {
        return in_array($this->status, [self::STATUS_LIVE, self::STATUS_SCHEDULED]);
    }

    /**
     * Get or create settings for this room.
     */
    public function getOrCreateSettings(): MeetRoomSetting
    {
        return $this->settings ?? $this->settings()->create();
    }
}
