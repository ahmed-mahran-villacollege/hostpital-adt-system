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

        $name = $this->faker->firstName($sex).' '.$this->faker->lastName($sex);

        return [
            'hospital_number' => $this->faker->unique()->numerify('HN#####'),
            'name' => $name,
            'date_of_birth' => $this->faker->dateTimeBetween('-90 years', '-16 years'),
            'sex' => $sex,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
