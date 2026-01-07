<?php

namespace App\Domain\MeetFA\Providers;

use App\Domain\MeetFA\Models\MeetEvent;
use App\Domain\MeetFA\Models\MeetProvider;
use App\Domain\MeetFA\Models\MeetRecording;
use App\Domain\MeetFA\Models\MeetRoom;
use App\Models\User;

/**
 * JitsiProvider - Jitsi Meet implementation
 *
 * Handles integration with self-hosted Jitsi Meet for video conferencing.
 * Uses the Jitsi external_api.js for iframe embedding.
 */
class JitsiProvider implements VideoProviderContract
{
    protected MeetProvider $provider;

    public function __construct(?MeetProvider $provider = null)
    {
        $this->provider = $provider ?? MeetProvider::jitsi();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'jitsi';
    }

    /**
     * {@inheritdoc}
     */
    public function createRoom(array $config): array
    {
        // Jitsi creates rooms on-the-fly, no API call needed
        return [
            'room_name' => $config['room_name'] ?? uniqid('meetfa-'),
            'provider' => $this->getName(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getJoinConfig(MeetRoom $room, User $user): array
    {
        $settings = $room->getOrCreateSettings();
        $providerConfig = $this->provider->config ?? [];

        return [
            'domain' => $this->getDomain(),
            'roomName' => $room->room_name,
            'width' => '100%',
            'height' => '100%',
            'parentNode' => null, // Set by frontend
            'userInfo' => [
                'displayName' => $user->name,
                'email' => $user->email,
            ],
            'configOverwrite' => [
                'startWithAudioMuted' => $settings->mute_on_start,
                'startWithVideoMuted' => $settings->camera_off_on_start,
                'prejoinPageEnabled' => false, // Skip pre-join screen
                'subject' => $room->title,
                'disableDeepLinking' => true,
                'disableInviteFunctions' => true,
                'lobby' => [
                    'autoKnock' => true,
                    'enableChat' => false,
                ],
                'enableLobbyChat' => false,
                'hideLobbyButton' => true,
                'requireDisplayName' => false,
                'startAudioOnly' => false,
                'toolbarButtons' => $this->getToolbarButtons($settings),
            ],
            'interfaceConfigOverwrite' => [
                'SHOW_JITSI_WATERMARK' => false,
                'SHOW_WATERMARK_FOR_GUESTS' => false,
                'SHOW_BRAND_WATERMARK' => false,
                'BRAND_WATERMARK_LINK' => '',
                'SHOW_POWERED_BY' => false,
                'MOBILE_APP_PROMO' => false,
                'TOOLBAR_ALWAYS_VISIBLE' => true,
                'DISABLE_JOIN_LEAVE_NOTIFICATIONS' => false,
                'filmStripOnly' => false,
            ],
            // Custom data for event tracking
            'meetfa' => [
                'room_id' => $room->id,
                'room_uuid' => $room->uuid,
                'user_id' => $user->id,
                'events_endpoint' => route('api.meet.events', ['uuid' => $room->uuid]),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function handleWebhook(array $payload): void
    {
        // Handle Jitsi webhook events (if using Jibri or custom webhook setup)
        $eventType = $payload['event_type'] ?? $payload['eventType'] ?? null;

        if (!$eventType) {
            return;
        }

        $roomName = $payload['room_name'] ?? $payload['roomName'] ?? null;

        if (!$roomName) {
            return;
        }

        $room = MeetRoom::where('room_name', $roomName)->first();

        if (!$room) {
            \Log::warning("Jitsi webhook: Room not found", ['room_name' => $roomName]);
            return;
        }

        // Create event record
        MeetEvent::create([
            'room_id' => $room->id,
            'event_type' => $this->mapEventType($eventType),
            'user_id' => null,
            'provider' => $this->getName(),
            'provider_event_id' => $payload['event_id'] ?? null,
            'payload' => $payload,
            'occurred_at' => now(),
        ]);

        // Handle specific events
        match ($this->mapEventType($eventType)) {
            MeetEvent::TYPE_RECORDING_STARTED => $this->handleRecordingStarted($room, $payload),
            MeetEvent::TYPE_RECORDING_READY => $this->handleRecordingReady($room, $payload),
            default => null,
        };
    }

    /**
     * {@inheritdoc}
     */
    public function startRecording(MeetRoom $room): array
    {
        // Jibri handles recording via Jitsi's internal mechanisms
        // This creates a pending recording record
        $recording = MeetRecording::create([
            'room_id' => $room->id,
            'status' => MeetRecording::STATUS_QUEUED,
            'started_at' => now(),
        ]);

        return [
            'recording_id' => $recording->id,
            'status' => $recording->status,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function stopRecording(MeetRoom $room): array
    {
        $recording = $room->recordings()
            ->whereIn('status', [MeetRecording::STATUS_RECORDING, MeetRecording::STATUS_QUEUED])
            ->latest()
            ->first();

        if ($recording) {
            $recording->update([
                'status' => MeetRecording::STATUS_PROCESSING,
                'ended_at' => now(),
            ]);
        }

        return [
            'recording_id' => $recording?->id,
            'status' => $recording?->status,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigured(): bool
    {
        return $this->provider !== null
            && !empty($this->provider->base_url)
            && $this->provider->is_active;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl(): string
    {
        return $this->provider->base_url ?? config('meetfa.jitsi_url', 'https://meet.jit.si');
    }

    /**
     * Get the domain without protocol.
     */
    protected function getDomain(): string
    {
        $url = $this->getBaseUrl();
        return preg_replace('#^https?://#', '', rtrim($url, '/'));
    }

    /**
     * Get toolbar buttons based on settings.
     */
    protected function getToolbarButtons(mixed $settings): array
    {
        $buttons = [
            'microphone',
            'camera',
            'desktop',
            'fullscreen',
            'chat',
            'raisehand',
            'participants-pane',
            'tileview',
            'hangup',
        ];

        if ($settings->allow_recording) {
            $buttons[] = 'recording';
        }

        return $buttons;
    }

    /**
     * Map Jitsi event types to MeetFA event types.
     */
    protected function mapEventType(string $eventType): string
    {
        return match ($eventType) {
            'participant_joined', 'participantJoined' => MeetEvent::TYPE_JOIN,
            'participant_left', 'participantLeft' => MeetEvent::TYPE_LEAVE,
            'recording_started', 'recordingStarted' => MeetEvent::TYPE_RECORDING_STARTED,
            'recording_stopped', 'recordingStopped' => MeetEvent::TYPE_RECORDING_STOPPED,
            'recording_ready', 'recordingReady' => MeetEvent::TYPE_RECORDING_READY,
            'room_created', 'roomCreated' => MeetEvent::TYPE_ROOM_CREATED,
            'room_destroyed', 'roomDestroyed' => MeetEvent::TYPE_ROOM_ENDED,
            default => $eventType,
        };
    }

    /**
     * Handle recording started event.
     */
    protected function handleRecordingStarted(MeetRoom $room, array $payload): void
    {
        $recording = $room->recordings()
            ->where('status', MeetRecording::STATUS_QUEUED)
            ->latest()
            ->first();

        if ($recording) {
            $recording->update([
                'status' => MeetRecording::STATUS_RECORDING,
                'provider_recording_id' => $payload['recording_id'] ?? null,
            ]);
        }
    }

    /**
     * Handle recording ready event.
     */
    protected function handleRecordingReady(MeetRoom $room, array $payload): void
    {
        $recordingId = $payload['recording_id'] ?? $payload['provider_recording_id'] ?? null;

        $recording = $room->recordings()
            ->where('provider_recording_id', $recordingId)
            ->orWhere(function ($q) {
                $q->where('status', MeetRecording::STATUS_PROCESSING);
            })
            ->latest()
            ->first();

        if ($recording) {
            $recording->update([
                'status' => MeetRecording::STATUS_READY,
                'ended_at' => now(),
                'duration_seconds' => $payload['duration'] ?? 0,
                'public_url' => $payload['url'] ?? null,
                'storage_path' => $payload['path'] ?? null,
                'metadata' => array_merge($recording->metadata ?? [], ['webhook_payload' => $payload]),
            ]);
        }
    }
}
