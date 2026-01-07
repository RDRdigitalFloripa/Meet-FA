<?php

namespace App\Domain\MeetFA\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MeetRoomSetting - Per-room configuration overrides
 *
 * @property int $id
 * @property int $room_id
 * @property bool $enable_lobby
 * @property bool $mute_on_start
 * @property bool $camera_off_on_start
 * @property bool $allow_recording
 * @property int|null $max_participants
 * @property array|null $settings
 */
class MeetRoomSetting extends Model
{
    protected $fillable = [
        'room_id',
        'enable_lobby',
        'mute_on_start',
        'camera_off_on_start',
        'allow_recording',
        'max_participants',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'enable_lobby' => 'boolean',
            'mute_on_start' => 'boolean',
            'camera_off_on_start' => 'boolean',
            'allow_recording' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(MeetRoom::class, 'room_id');
    }
}
