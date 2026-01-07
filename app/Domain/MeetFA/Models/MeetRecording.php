<?php

namespace App\Domain\MeetFA\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MeetRecording - Recording metadata and storage info
 *
 * @property int $id
 * @property int $room_id
 * @property string|null $provider_recording_id
 * @property string $status
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $ended_at
 * @property int $duration_seconds
 * @property string|null $storage_disk
 * @property string|null $storage_path
 * @property string|null $public_url
 * @property string|null $checksum
 * @property array|null $metadata
 */
class MeetRecording extends Model
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_RECORDING = 'recording';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY = 'ready';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'room_id',
        'provider_recording_id',
        'status',
        'started_at',
        'ended_at',
        'duration_seconds',
        'storage_disk',
        'storage_path',
        'public_url',
        'checksum',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(MeetRoom::class, 'room_id');
    }

    // Status helpers

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, [self::STATUS_RECORDING, self::STATUS_PROCESSING]);
    }

    /**
     * Get the full storage path.
     */
    public function getFullPath(): ?string
    {
        if (!$this->storage_disk || !$this->storage_path) {
            return null;
        }

        return \Storage::disk($this->storage_disk)->path($this->storage_path);
    }

    /**
     * Get download URL.
     */
    public function getDownloadUrl(): ?string
    {
        if ($this->public_url) {
            return $this->public_url;
        }

        if ($this->storage_disk && $this->storage_path) {
            return \Storage::disk($this->storage_disk)->url($this->storage_path);
        }

        return null;
    }
}
