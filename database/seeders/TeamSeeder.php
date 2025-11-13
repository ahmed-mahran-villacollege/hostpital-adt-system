<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeamSeeder extends Seeder
{
    protected $names = [
        'Orthopaedics A',
        'Orthopaedics B',
        'Paediatrics',
        'Surgery A',
        'Surgery B',
        'Obstetrics A',
        'Obstetrics B',
        'Psychiatry',
        'Cardiology',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->names as $name) {
            Team::create([
                'name' => $name,
                'code' => "DT-0" . random_int(100, 500),
                'consultant_id' => Doctor::factory()->create(['type' => 'Consultant'])->id,
                'junior_id' => Doctor::factory()->create(['type' => 'Junior'])->id,
            ]);
        }
    }
}
