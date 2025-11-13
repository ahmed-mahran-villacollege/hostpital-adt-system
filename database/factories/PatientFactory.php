<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sex = $this->faker->randomElement(['Male', 'Female']);

        return [
            'hospital_number' => $this->faker->unique()->numerify('HN#####'),
            'first_name' => $this->faker->firstName($sex),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->dateTimeBetween('-90 years', '-16 years'),
            'sex' => $sex,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
