<?php

namespace Database\Factories;

use App\Models\Taxa;
use App\Models\CountForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormRow>
 */
class FormRowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $count_forms = CountForm::pluck('id')->toArray();
        $taxa = Taxa::pluck('id')->toArray();
        $ranks = ["order", "family", "subfamily", "tribe", "genus", "species", "subspecies"];
        return [
            'count_form_id' => $count_forms[rand(0, count($count_forms)-1)],
            'sl_no' => fake()->randomNumber(2),
            'common_name' => fake()->name(),
            'scientific_name' => fake()->name(),
            'taxa_id' => $taxa[rand(0, count($taxa)-1)],
            'individuals' => fake()->randomDigit(),
            'no_of_individuals_cleaned' => fake()->randomDigit(),
            'remarks' => fake()->sentence(),
            'id_quality' => $ranks[rand(0,6)],
            'flag' => fake()->boolean(),
            'flag_notes' => fake()->sentences(5, true),
        ];
    }
}
