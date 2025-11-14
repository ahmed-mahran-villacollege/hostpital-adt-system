<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Ward;
use App\Models\Patient;
use App\Models\Admission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Patient::all() as $patient) {
            Admission::create([
                "patient_id" => $patient->id,
                "ward_id" => Ward::inRandomOrder()->first()->id,
                "team_id" => Team::inRandomOrder()->first()->id,
                "admitted_at" => now()->subDays(random_int(0, 20))->toDateTimeString(),
            ]);
        }
    }
}
