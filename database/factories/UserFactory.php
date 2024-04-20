<?php

namespace Database\Factories;

use App\Models\User;
use App\Services\HelperService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName,
            'email' => fake()->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
//            'first_name' => HelperService::encryptAndStripTags(fake()->firstName),
//            'last_name' => HelperService::encryptAndStripTags(fake()->lastName),
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'phone' => HelperService::encryptAndStripTags(fake()->phoneNumber),
            'address' => HelperService::encryptAndStripTags(fake()->address),
            'birth_date' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female']),
            'country' => fake()->countryCode(),
            'city' => fake()->city,
            'zip_code' => fake()->postcode(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure()
    {
        $increment = 0;
        return $this->afterMaking(function (User $user) use (&$increment) {
            $user->username = "user_$increment";
            $user->save();
            $increment++;
        });
    }
}
