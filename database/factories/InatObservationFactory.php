<?php

namespace Database\Factories;

use App\Models\Taxa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InatObservation>
 */
class InatObservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $user_ids = User::where("source", "inat")->pluck('id')->toArray();
        $taxa = Taxa::pluck('id')->toArray();
        $ranks = ["order", "family", "subfamily", "tribe", "genus", "species", "subspecies"];
        return [
            'user_id' => $user_ids[rand(0, count($user_ids)-1)],
            'taxa_id' => $taxa[rand(0, count($taxa)-1)],
            'observed_on' => fake()->date(),
            'location' => fake()->city(),
            'place_guess' => fake()->address(),
            'state' => fake()->state(),
            'district' => fake()->city(),
            'img_url' => fake()->words(5, true),
            'is_lepidoptera' => fake()->boolean(),
            'description' => fake()->sentences(3, true),
            'quality_grade' => $ranks[rand(0,6)],
            'license_code' => fake()->word(),
            'oauth_application_id' => fake()->randomNumber(2, true),
            'inat_created_at' => fake()->date(),
            'inat_updated_at' => fake()->date(),
            'flag' => fake()->boolean(),
            'flag_notes' => fake()->sentences(2, true),
            //
        ];
    }
}
