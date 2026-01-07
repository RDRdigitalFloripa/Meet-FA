<?php

namespace App\Domain\MeetFA\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MeetProvider - Video provider configuration (Jitsi, LiveKit)
 *
 * @property int $id
 * @property string $name
 * @property string $base_url
 * @property array|null $config
 * @property bool $is_active
 */
class MeetProvider extends Model
{
    protected $fillable = [
        'name',
        'base_url',
        'config',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all rooms using this provider.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(MeetRoom::class, 'provider_id');
    }

    /**
     * Scope to get only active providers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the default (active) Jitsi provider.
     */
    public static function jitsi(): ?self
    {
        return static::where('name', 'jitsi')->active()->first();
    }
}
