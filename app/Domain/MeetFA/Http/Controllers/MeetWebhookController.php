<?php

namespace App\Domain\MeetFA\Http\Controllers;

use App\Domain\MeetFA\Providers\JitsiProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * MeetWebhookController - Handles webhooks from video providers
 */
class MeetWebhookController extends Controller
{
    /**
     * Handle Jitsi webhook.
     */
    public function jitsi(Request $request, JitsiProvider $provider)
    {
        $payload = $request->all();

        Log::info('Jitsi webhook received', ['payload' => $payload]);

        try {
            $provider->handleWebhook($payload);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Jitsi webhook error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
