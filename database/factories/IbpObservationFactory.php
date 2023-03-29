<?php

namespace Database\Factories;

use App\Models\Taxa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IbpObservation>
 */
class IbpObservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user_ids = User::where("source", "ibp")->pluck('id')->toArray();
        $taxa = Taxa::pluck('id')->toArray();
        $ranks = ["order", "family", "subfamily", "tribe", "genus", "species", "subspecies"];
        return [
            'user_id' => $user_ids[rand(0, count($user_ids)-1)],
            'taxa_id' => $taxa[rand(0, count($taxa)-1)],
            'createdBy' => fake()->name(),
            'placeName' => fake()->address(),
            'flagNotes' => fake()->sentences(3, true),
            'createdOn' => fake()->date(),
            'associatedMedia' => fake()->words(5, true),
            'locationLat' =>fake()->latitude(6, 36) ,
            'locationLon' => fake()->longitude(68, 98) ,
            'fromDate' => fake()->date(),
            'rank' => $ranks[rand(0,6)],
            'scientificName' => fake()->name(),
            'commonName' => fake()->name(),
            'higherClassificationId' => fake()->name(),
            'state' => fake()->state(),
            'observedInMonth' => fake()->monthName(),
            'scientific_name_cleaned' => fake()->name(),
            'flag' => fake()->boolean(),
            'flag_notes' => fake()->sentences(2, true),
        ];
    }
}
