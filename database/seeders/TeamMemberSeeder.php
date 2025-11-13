<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Team::all() as $team) {
            foreach (range(1, 5) as $index) {
                TeamMember::create([
                    'team_id' => $team->id,
                    'doctor_id' => Doctor::factory()->create([
                        'type' => 'Junior',
                        'grade' => random_int(1, 4),
                    ])->id,
                ]);
            }
        }
    }
}
