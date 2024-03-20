<?php

namespace Database\Factories;

use App\Models\VotingRoom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VotingRoom>
 */
class VotingRoomFactory extends Factory
{
    protected $model = VotingRoom::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_name' => Crypt::encryptString(fake()->sentence),
            'room_description' => Crypt::encryptString(fake()->paragraph),
            'start_time' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'end_time' => fake()->dateTimeBetween('+1 week', '+2 weeks'),
            'timezone' => fake()->timezone,
        ];
    }
}
