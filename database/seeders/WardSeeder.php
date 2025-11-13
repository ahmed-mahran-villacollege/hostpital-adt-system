<?php

namespace Database\Seeders;

use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    protected $names = [
        'General Ward A',
        'General Ward B',
        'Maternity Ward',
        'Pediatric Ward',
        'Psychiatric Ward',
        'Urology Ward',
        'Orthopedic Ward',
        'Surgical Ward',
        'Cardiac Ward',
        'Neurology Ward',
    ];

    protected $types = ['Male', 'Female'];

    private function type()
    {
        return $this->types[array_rand($this->types)];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->names as $name) {
            Ward::create([
                'name' => $name,
                'type' => str_contains($name, 'Maternity') || str_contains($name, 'Pediatric') ? 'Female' : $this->type(),
                'capacity' => rand(10, 50),
            ]);
        }
    }
}