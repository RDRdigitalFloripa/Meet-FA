<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$room = \App\Domain\MeetFA\Models\MeetRoom::first();
$user = \App\Models\User::first();
$guard = new \App\Domain\MeetFA\Services\JoinGuardService();

$result = $guard->canJoin($room, $user);

echo "Room ID: " . $room->id . "\n";
echo "Room created_by: " . $room->created_by_user_id . "\n";
echo "User ID: " . $user->id . "\n";
echo "Can Join: " . ($result['allowed'] ? 'YES' : 'NO') . "\n";
echo "Reason: " . ($result['reason'] ?? 'N/A') . "\n";
