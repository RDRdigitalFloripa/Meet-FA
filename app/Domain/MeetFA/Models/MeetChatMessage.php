<?php

namespace App\Domain\MeetFA\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MeetChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'message',
        'is_ai',
    ];

    protected $casts = [
        'is_ai' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(MeetRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
