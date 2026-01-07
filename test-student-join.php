<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the existing room
$room = \App\Domain\MeetFA\Models\MeetRoom::first();

// Get the student user (assumes it was just created e has ID 2 or is search by email)
$student = \App\Models\User::where('email', 'aluno@meetfa.test')->first();
$professor = \App\Models\User::find($room->created_by_user_id);

$guard = new \App\Domain\MeetFA\Services\JoinGuardService();

echo "--- Room Info ---\n";
echo "Room ID: " . $room->id . "\n";
echo "Status: " . $room->status . "\n";
echo "Created By: " . $professor->name . " (ID: " . $professor->id . ")\n\n";

echo "--- Student Access Check ---\n";
echo "Student: " . $student->name . " (ID: " . $student->id . ")\n";
$result = $guard->canJoin($room, $student);
echo "Can Join: " . ($result['allowed'] ? 'YES' : 'NO') . "\n";
echo "Reason: " . ($result['reason'] ?? 'N/A') . "\n";
