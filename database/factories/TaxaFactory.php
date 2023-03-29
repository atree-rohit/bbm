<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Taxa>
 */
class TaxaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ranks = ["order", "family", "subfamily", "tribe", "genus", "species", "subspecies"];
        return [
            'name' => fake()->name(),
            'common_name' => fake()->name(),
            'rank' => $ranks[rand(0,6)],
            'ancestry' => implode("/", fake()->randomElements([1,2,3,4,5,6,7,8,9],8)),
        ];
    }
}
