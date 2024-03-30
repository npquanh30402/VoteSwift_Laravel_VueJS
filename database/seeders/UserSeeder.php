<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Question;
use App\Models\User;
use App\Models\Vote;
use App\Models\VotingRoom;
use App\Models\VotingRoomSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        for ($i = 0; $i < 5; $i++) {
//            DB::table('users')->insert([
//                'username' => 'user_' . $i,
//                'email' => Str::random(10) . '@example.com',
//                'password' => Hash::make('123'),
//            ]);
//        }

        DB::transaction(function () {
            User::factory(5)->has(
                VotingRoom::factory(random_int(5, 8))
                    ->has(VotingRoomSetting::factory(1), 'settings')
                    ->has(
                        Question::factory(random_int(2, 5))->afterCreating(function (Question $question) {
                            // Generate a random number of candidates for the question
                            $numCandidates = random_int(2, 5);

                            // Create candidates for the question
                            $candidates = Candidate::factory($numCandidates)->create([
                                'question_id' => $question->id,
                            ]);

                            // Get all users
                            $users = User::all();

                            // Iterate over each candidate and allow users to vote for it
                            $candidates->each(function ($candidate) use ($users) {
                                $voters = $users->random(random_int(1, count($users) - 1));
                                foreach ($voters as $voter) {
                                    // Ensure each user votes for each candidate only once
                                    if (!$voter->hasVotedForCandidate($candidate)) {
                                        Vote::factory()->create([
                                            'candidate_id' => $candidate->id,
                                            'user_id' => $voter->id,
                                        ]);
                                    }
                                }
                            });
                        })
                    ), 'rooms'
            )->create();
        });


    }
}
