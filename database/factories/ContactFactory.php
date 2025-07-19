<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            
            // NOMOR TELEPON SEKARANG SELALU FORMAT INDONESIA
            'phone_number' => '628' . $this->faker->unique()->numerify('##########'),

            'email' => $this->faker->boolean(80) ? $this->faker->unique()->safeEmail() : null,
            'status' => 'new',
            'notes' => $this->faker->boolean(50) ? $this->faker->sentence() : null,
        ];
    }
}