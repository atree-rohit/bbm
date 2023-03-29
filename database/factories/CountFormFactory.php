<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CountForm>
 */
class CountFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user_ids = User::where("source", "count")->pluck('id')->toArray();
        return [
            'user_id' => $user_ids[rand(0, count($user_ids)-1)],
            'name' => fake()->name(),
            'affiliation' => fake()->words(4, true) ,
            'phone' => fake()->phoneNumber() ,
            'email' => fake()->email() ,
            'team_members' => fake()->name() ,
            'photo_link' => fake()->sentence(10) ,
            'location' => fake()->city() ,
            'state' => fake()->state() ,
            'district' => fake()->city() ,
            'coordinates' => fake()->randomFloat(2) ,
            'latitude' => fake()->latitude(6, 36) ,
            'longitude' => fake()->longitude(68, 98) ,
            'date' => fake()->date() ,
            'start_time' => fake()->time() ,
            'end_time' => fake()->time() ,
            'altitude' => fake()->randomFloat(2, 0, 5000) ,
            'distance' => fake()->randomFloat(2, 500, 1500) ,
            'weather' => fake()->words(2, true) ,
            'comments' => fake()->sentence() ,
            'file' => fake()->word(),
            'original_filename' => fake()->word() ,
            'duplicate' => fake()->boolean() ,
            'validated' => fake()->boolean() ,
            'flag' => fake()->boolean() ,
            'flag_notes' => fake()->sentence() ,
        ];
    }
}
