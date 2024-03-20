<?php

namespace Database\Seeders;

use App\Models\VotingRoomSetting;
use Illuminate\Database\Seeder;

class VotingRoomSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VotingRoomSetting::factory(100)->create();
    }
}
