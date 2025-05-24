<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->slug,
            'title' => $this->faker->sentence,
            'short_description' => $this->faker->paragraph,
            'description' => $this->faker->paragraph,
            'language' => $this->faker->languageCode,
            'inclusions' => $this->faker->paragraph,
            'exclusions' => $this->faker->paragraph,
            'itinerary' => $this->faker->paragraph,
            'what_to_bring' => $this->faker->paragraph,
            'what_to_wear' => $this->faker->paragraph,
            'what_to_expect' => $this->faker->paragraph,
            'what_to_know' => $this->faker->paragraph,
            'remarks' => $this->faker->paragraph,
            'meeting_instructions' => $this->faker->paragraph,
            'cancellation_policy' => $this->faker->paragraph,
            'refund_policy' => $this->faker->paragraph,
            'health_and_safety' => $this->faker->paragraph,
            'thumbnail' => $this->faker->imageUrl,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'is_active' => true,
        ];
    }
}
