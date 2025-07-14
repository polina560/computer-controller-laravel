<?php

namespace Database\Factories;

use App\Models\Text;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Text>
 */
class TextFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->biasedNumberBetween(1, 10),
            'value' => fake()->paragraph(),
        ];
    }
}
