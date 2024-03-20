<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\VotingRoom;
use Illuminate\Database\Seeder;

class VotingRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VotingRoom::factory(100)->has(Question::factory()->count(random_int(1, 5)))->create();
    }
}
