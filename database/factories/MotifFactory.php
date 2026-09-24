<?php

namespace Database\Factories;

use App\Models\Motif;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Motif>
 */
class MotifFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(),
            'date' => $this->faker->date(),
        ];
    }
}
