<?php

namespace Database\Seeders;

use App\Models\Ward;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    protected $names = [
        'General Ward',
        'Pediatric Ward',
        'Psychiatric Ward',
        'Orthopedic Ward',
        'Surgical Ward',
        'Cardiology Ward',
        'Neurology Ward',
    ];

    protected $types = ['Male', 'Female'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->names as $name) {
            foreach ($this->types as $type) {
                Ward::create([
                    'name' => $name . ' ' . ($type === 'Male' ? 'A' : 'B'),
                    'type' => $type,
                    'capacity' => rand(10, 25),
                ]);
            }
        }
    }
}
