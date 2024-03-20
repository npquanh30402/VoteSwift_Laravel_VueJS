<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory(100)->has(Candidate::factory()->count(random_int(1, 5)))->create();
    }
}
