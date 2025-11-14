<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rank = $this->faker->randomElement(['Consultant', 'Junior']);

        $name = 'Dr. ' . $this->faker->firstName . ' ' . $this->faker->lastName;

        return [
            'name' => $name,
            'rank' => $rank,
            'grade' => $rank === 'Consultant' ? 5 : $this->faker->randomElement([1, 2, 3, 4]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
