<?php

use App\Domain\MeetFA\Http\Controllers\MeetEventController;
use App\Domain\MeetFA\Http\Controllers\MeetWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// MeetFA API Routes
Route::prefix('meet')->name('api.meet.')->group(function () {
    // Events from frontend (requires authentication)
    Route::post('/rooms/{uuid}/events', [MeetEventController::class, 'store'])
        ->name('events');

    // Webhooks from providers (no auth, uses signature validation)
    Route::post('/webhook/jitsi', [MeetWebhookController::class, 'jitsi'])
        ->name('webhook.jitsi');
});
