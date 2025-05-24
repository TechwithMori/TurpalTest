<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'invoice_id' => Invoice::factory(),
            'type' => $this->faker->randomElement(['EXPERIENCE', 'PACKAGE']),
            'experience_id' => Experience::factory(),
            'availability_id' => Availability::factory(),
            'pax' => $this->faker->numberBetween(1, 10),
            'execute_date' => $this->faker->date(),
            'buy_price' => $this->faker->randomFloat(2, 0, 1000),
            'sell_price' => $this->faker->randomFloat(2, 0, 1000),
            'vat_amount' => $this->faker->randomFloat(2, 0, 1000),
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'start' => $this->faker->dateTime(),
            'end' => $this->faker->dateTime(),
        ];
    }
}
