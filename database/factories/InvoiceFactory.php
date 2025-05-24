<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
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
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'channel' => $this->faker->randomElement(['online', 'offline']),
            'date' => $this->faker->date(),
            'buyer_name' => $this->faker->name(),
            'buyer_email' => $this->faker->email(),
            'buyer_phone' => $this->faker->phoneNumber(),
            'buyer_address' => $this->faker->address(),
            'buyer_city' => $this->faker->city(),
            'buyer_country' => $this->faker->countryCode(),
        ];
    }
}
