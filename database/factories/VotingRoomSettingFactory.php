<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VotingRoomSetting>
 */
class VotingRoomSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_visibility' => fake()->boolean,
            'password' => null,
            'results_visibility' => fake()->randomElement(['after_voting', 'restricted']),
            'allow_anonymous_voting' => fake()->boolean,
        ];
    }
}
