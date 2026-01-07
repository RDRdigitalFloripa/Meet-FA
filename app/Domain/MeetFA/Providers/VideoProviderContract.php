<?php

namespace App\Domain\MeetFA\Providers;

use App\Domain\MeetFA\Models\MeetRoom;
use App\Models\User;

/**
 * VideoProviderContract - Interface for video providers (Jitsi, LiveKit, etc.)
 *
 * This abstraction allows switching or adding video providers without
 * modifying the core MeetFA logic.
 */
interface VideoProviderContract
{
    /**
     * Get the provider name.
     */
    public function getName(): string;

    /**
     * Create a room on the provider side.
     *
     * @param array $config Room configuration
     * @return array Provider-specific room data
     */
    public function createRoom(array $config): array;

    /**
     * Get the configuration needed to join a room.
     *
     * @param MeetRoom $room The room to join
     * @param User $user The user joining
     * @return array Configuration for the frontend embed
     */
    public function getJoinConfig(MeetRoom $room, User $user): array;

    /**
     * Handle incoming webhook from the provider.
     *
     * @param array $payload Webhook payload
     * @return void
     */
    public function handleWebhook(array $payload): void;

    /**
     * Start recording a room.
     *
     * @param MeetRoom $room
     * @return array Recording information
     */
    public function startRecording(MeetRoom $room): array;

    /**
     * Stop recording a room.
     *
     * @param MeetRoom $room
     * @return array Recording information
     */
    public function stopRecording(MeetRoom $room): array;

    /**
     * Check if the provider is properly configured.
     */
    public function isConfigured(): bool;

    /**
     * Get the base URL for the provider.
     */
    public function getBaseUrl(): string;
}
