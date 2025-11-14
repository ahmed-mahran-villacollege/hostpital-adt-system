<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Patient;
use App\Models\Team;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class AdmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Patient::all() as $patient) {
            Admission::create([
                'patient_id' => $patient->id,
                'ward_id' => Ward::whereType($patient->sex)->inRandomOrder()->first()->id,
                'team_id' => Team::inRandomOrder()->first()->id,
                'admitted_at' => now()
                    ->subDays(random_int(5, 20))
                    ->subSeconds(random_int(0, 86400))
                    ->toDateTimeString(),
            ]);
        }
    }
}
