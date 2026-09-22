<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'tin' => fake()->numerify('###-###-###-###'),
            'registered_name' => fake()->company(),
            'trade_name' => fake()->company(),
            'branch_code' => '000',

            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => null,
            'barangay' => fake()->citySuffix(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country_code' => 'PH',

            'company_email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
