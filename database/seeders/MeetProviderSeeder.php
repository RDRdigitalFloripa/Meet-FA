<?php

namespace Database\Seeders;

use App\Domain\MeetFA\Models\MeetProvider;
use Illuminate\Database\Seeder;

class MeetProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Jitsi provider
        MeetProvider::updateOrCreate(
            ['name' => 'jitsi'],
            [
                'base_url' => env('JITSI_BASE_URL', 'https://meet.jit.si'),
                'config' => [
                    'subject' => 'MeetFA - Faculdade Anasps',
                    'prejoinPageEnabled' => false,
                    'disableInviteFunctions' => true,
                ],
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Jitsi provider created/updated');
    }
}
