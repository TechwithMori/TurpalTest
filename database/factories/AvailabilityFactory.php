<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Experience;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'experience_id' => Experience::factory(),
            'price' => $this->faker->randomFloat(),
            'start_time' => now()->toDateTimeString(),
            'end_time' => now()->addWeek()->toDateTimeString(),
        ];
    }
}
