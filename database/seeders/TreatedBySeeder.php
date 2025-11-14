<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\TreatedBy;
use Illuminate\Database\Seeder;

class TreatedBySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Admission::all() as $admission) {
            foreach (range(1, random_int(1, 5)) as $index) {

                random_int(0, 1) ? $doctor = $admission->team->consultant->id : $doctor = $admission->team->teamMembers->random()->doctor_id;

                TreatedBy::create([
                    'admission_id' => $admission->id,
                    'doctor_id' => $doctor,
                    'treated_at' => $admission->admitted_at
                        ->addDays(random_int(0, 5))
                        ->addSeconds(random_int(0, 86400))
                        ->toDateTimeString(),
                ]);
            }
        }
    }
}
