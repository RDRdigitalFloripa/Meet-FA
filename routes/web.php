<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// MeetFA Routes
use App\Domain\MeetFA\Http\Controllers\MeetRoomController;

Route::middleware(['auth'])->group(function () {
    // Public room list (authenticated users)
    Route::get('/meet', [MeetRoomController::class, 'index'])->name('meet.index');

    // Create room form
    Route::get('/meet/create', [MeetRoomController::class, 'create'])->name('meet.create');

    // Store new room
    Route::post('/meet', [MeetRoomController::class, 'store'])->name('meet.store');

    // View/join room
    Route::get('/meet/{uuid}', [MeetRoomController::class, 'show'])->name('meet.show');

    // Room report
    Route::get('/meet/{uuid}/report', [MeetRoomController::class, 'report'])->name('meet.report');

    // Start room (AJAX)
    Route::post('/meet/{uuid}/start', [MeetRoomController::class, 'start'])->name('meet.start');

    // End room (AJAX)
    Route::post('/meet/{uuid}/end', [MeetRoomController::class, 'end'])->name('meet.end');

    // Chat Actions (AJAX)
    Route::get('/meet/{uuid}/chat', [App\Domain\MeetFA\Http\Controllers\MeetChatController::class, 'index'])->name('meet.chat.index');
    Route::post('/meet/{uuid}/chat', [App\Domain\MeetFA\Http\Controllers\MeetChatController::class, 'store'])->name('meet.chat.store');
});

