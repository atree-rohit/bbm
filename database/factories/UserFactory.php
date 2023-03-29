<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user_sources = ["count", "inat", "ibp", "ifb", null];
        return [
            'user_name' => fake()->name(),
            'user_login' => fake()->userName(),
            'source' => $user_sources[rand(0,4)],
        ];
    }
}
