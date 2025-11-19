<?php

use App\Filament\Resources\Admissions\Pages\ViewAdmission;
use App\Filament\Resources\Admissions\RelationManagers\TreatedByRelationManager;
use App\Filament\Resources\Patients\Pages\ViewPatient;
use App\Filament\Resources\Teams\Pages\ViewTeam;
use App\Filament\Resources\Wards\Pages\ViewWard;
use App\Filament\Resources\Wards\RelationManagers\PatientsRelationManager;
use App\Models\Admission;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\TreatedBy;
use Livewire\Livewire;

it('lists patients on a ward showing name and age', function () {
    $user = testUserWithPermissions('view.ward_patient_list');
    $ward = testWard('Male');
    $team = testTeam();

    $patient = Patient::factory()->create([
        'sex' => 'Male',
        'date_of_birth' => now()->subYears(30),
    ]);

    Admission::create([
        'patient_id' => $patient->id,
        'ward_id' => $ward->id,
        'team_id' => $team->id,
    ]);

    $expectedAge = now()->format('Y') - $patient->date_of_birth->format('Y');

    Livewire::actingAs($user)
        ->test(PatientsRelationManager::class, [
            'ownerRecord' => $ward,
            'pageClass' => ViewWard::class,
        ])
        ->assertSee($patient->name)
        ->assertSee((string) $expectedAge);
});

it('lists patients cared for by a team showing patient and ward', function () {
    $user = testUserWithPermissions('view.team_patient_list');
    $ward = testWard('Male');
    $team = testTeam();

    $patient = Patient::factory()->create([
        'sex' => 'Male',
    ]);

    Admission::create([
        'patient_id' => $patient->id,
        'ward_id' => $ward->id,
        'team_id' => $team->id,
    ]);

    Livewire::actingAs($user)
        ->test(PatientsRelationManager::class, [
            'ownerRecord' => $team,
            'pageClass' => ViewTeam::class,
        ])
        ->assertSee($patient->name)
        ->assertSee($ward->name);
});

it('shows treated by list with consultant, team code, and junior grades', function () {
    $user = testUserWithPermissions('view.patient_treatment_list');
    $ward = testWard('Male');
    $team = testTeam();

    $consultant = $team->consultant;
    $junior = Doctor::factory()->create(['rank' => 'Junior', 'grade' => 2]);
    $team->doctors()->attach($junior->id);

    $patient = Patient::factory()->create(['sex' => 'Male']);
    $admission = Admission::create([
        'patient_id' => $patient->id,
        'ward_id' => $ward->id,
        'team_id' => $team->id,
    ]);

    $admission->treatedBy()->create([
        'doctor_id' => $consultant->id,
        'treated_at' => now()->subDay(),
    ]);

    $admission->treatedBy()->create([
        'doctor_id' => $junior->id,
        'treated_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(ViewPatient::class, ['record' => $patient->id])
        ->assertSee($consultant->name)
        ->assertSee($team->code);

    Livewire::actingAs($user)
        ->test(TreatedByRelationManager::class, [
            'ownerRecord' => $admission,
            'pageClass' => ViewAdmission::class,
        ])
        ->assertSee($consultant->name)
        ->assertSee("{$junior->name} (Gr. {$junior->grade})");
});
