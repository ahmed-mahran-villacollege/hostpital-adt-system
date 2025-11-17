<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $hospitalAdmin = User::factory()->create([
            'name' => 'Hospital Administrator',
            'email' => 'admin@example.com',
        ]);

        $this->call([
            RoleSeeder::class,
            WardSeeder::class,
            PatientSeeder::class,
            TeamSeeder::class,
            TeamMemberSeeder::class,
            AdmissionSeeder::class,
            TreatedBySeeder::class,
        ]);

        $hospitalAdmin->assignRole('hospital_admin');
    }
}
