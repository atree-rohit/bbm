<?php

namespace Database\Factories;

use App\Models\Taxa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class IfbObservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user_ids = User::where("source", "ifb")->pluck('id')->toArray();
        $taxa = Taxa::pluck('id')->toArray();
        $ranks = ["order", "family", "subfamily", "tribe", "genus", "species", "subspecies"];
        return [
            'user_id' => $user_ids[rand(0, count($user_ids)-1)],
            'taxa_id' => $taxa[rand(0, count($taxa)-1)],
            'created_date' => fake()->date(),
            'observed_date' => fake()->date(),
            'media_code' => fake()->words(5, true),
            'species_name' => fake()->name(),
            'rank' => $ranks[rand(0,6)],
            'user_name' => fake()->name(),
            'life_stage' => fake()->word(),
            'country' => fake()->country(),
            'state' => fake()->state(),
            'district' => fake()->city(),
            'location_name' => fake()->address(),
            'latitude' => fake()->latitude(6, 36) ,
            'longitude' => fake()->longitude(68, 98) ,
            'flag' => fake()->boolean(),
            'flag_notes' => fake()->sentences(2, true),
        ];
    }
}
