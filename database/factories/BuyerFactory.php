<?php

namespace Database\Factories;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuyerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'seller_id' => Seller::factory(),

            'tin' => fake()->numerify('###-###-###-###'),
            'registered_name' => fake()->company(),
            'trade_name' => fake()->company(),
            'customer_code' => 'CUS-' . fake()->unique()->numerify('######'),

            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => null,
            'barangay' => fake()->citySuffix(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country_code' => 'PH',

            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
