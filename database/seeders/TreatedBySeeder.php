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
            TreatedBy::create([
                'admission_id' => $admission->id,
                'doctor_id' => $admission->team->teamMembers->random()->doctor_id,
                'treated_at' => $admission->admitted_at
                    ->addDays(random_int(0, 5))
                    ->addSeconds(random_int(0, 86400))
                    ->toDateTimeString(),
            ]);
        }
    }
}
