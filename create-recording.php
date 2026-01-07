<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$room = \App\Domain\MeetFA\Models\MeetRoom::first();
\App\Domain\MeetFA\Models\MeetRecording::create([
    'room_id' => $room->id,
    'status' => 'ready',
    'started_at' => now()->subHour(),
    'ended_at' => now()->subMinutes(10),
    'duration_seconds' => 3000,
    'public_url' => 'https://example.com/gravacao.mp4'
]);

echo "Recording created for Room: " . $room->title;
