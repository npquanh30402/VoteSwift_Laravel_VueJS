<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_title' => Crypt::encryptString(fake()->sentence),
            'question_description' => Crypt::encryptString(fake()->paragraph),
//            'voting_room_id' => fake()->randomElement($votingRoomIds),
        ];
    }
}
