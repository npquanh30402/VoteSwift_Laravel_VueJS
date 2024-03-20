<?php

namespace Database\Factories;

use App\Models\VotingRoomSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class VotingRoomSettingsFactory extends Factory
{
    protected $model = VotingRoomSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'allow_multiple_votes' => fake()->boolean,
            'public_visibility' => fake()->boolean,
            'password' => fake()->nullable ? fake()->password : null,
            'results_visibility' => fake()->randomElement(['after_voting', 'restricted']),
            'allow_voting' => fake()->boolean,
            'allow_skipping' => fake()->boolean,
            'allow_anonymous_voting' => fake()->boolean,
        ];
    }
}
