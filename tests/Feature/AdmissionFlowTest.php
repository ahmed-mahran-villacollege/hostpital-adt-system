<?php

use App\Filament\Pages\DischargePatient;
use App\Filament\Pages\TransferPatient;
use App\Filament\Resources\Admissions\Pages\CreateAdmission;
use App\Models\Admission;
use App\Models\Patient;
use Livewire\Livewire;

it('creates an admission with a new patient and validates ward assignment', function () {
    $user = testUserWithPermissions('patient.admit');
    $ward = testWard('Female');
    $team = testTeam();

    Livewire::actingAs($user)
        ->test(CreateAdmission::class)
        ->set('data', [
            'ward_id' => $ward->id,
            'team_id' => $team->id,
            'admitted_at' => now()->toDateString(),
            'patient' => [
                'hospital_number' => 'HN'.rand(1000, 9999),
                'name' => 'Jane Doe',
                'date_of_birth' => now()->subYears(30)->toDateString(),
                'sex' => 'Female',
            ],
        ])
        ->call('create')
        ->assertHasNoErrors();

    expect(Admission::count())->toBe(expected: 1);
    $admission = Admission::first();
    expect($admission->patient)->not->toBeNull();
    expect($admission->ward_id)->toBe($ward->id);
    expect($admission->team_id)->toBe($team->id);
});

it('prevents transferring to an incompatible ward', function () {
    $user = testUserWithPermissions('patient.transfer');
    $maleWard = testWard('Male');
    $femaleWard = testWard('Female');
    $team = testTeam();

    $admission = Admission::create([
        'patient_id' => Patient::factory()->create(['sex' => 'Male'])->id,
        'ward_id' => $maleWard->id,
        'team_id' => $team->id,
        'admitted_at' => now()->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test(TransferPatient::class)
        ->set('data', [
            'admission_id' => $admission->id,
            'destination_ward_id' => $femaleWard->id,
        ])
        ->call('transfer')
        ->assertHasErrors(['destination_ward_id']);

    expect($admission->fresh()->ward_id)->toBe($maleWard->id);
});

it('transfers a patient to a new ward', function () {
    $user = testUserWithPermissions('patient.transfer');
    $maleWard = testWard('Male');
    $newWard = testWard('Male');
    $team = testTeam();
    $patient = Patient::factory()->create(['sex' => 'Male']);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'ward_id' => $maleWard->id,
        'team_id' => $team->id,
    ]);

    Livewire::actingAs($user)
        ->test(TransferPatient::class)
        ->set('data', [
            'admission_id' => $admission->id,
            'destination_ward_id' => $newWard->id,
        ])
        ->call('transfer')
        ->assertHasNoErrors();

    expect($admission->fresh()->ward_id)->toBe($newWard->id);
});

it('discharges a patient and removes the admission', function () {
    $user = testUserWithPermissions('patient.discharge');
    $ward = testWard('Male');
    $team = testTeam();
    $patient = Patient::factory()->create(['sex' => 'Male']);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'ward_id' => $ward->id,
        'team_id' => $team->id,
    ]);

    Livewire::actingAs($user)
        ->test(DischargePatient::class)
        ->set('data', [
            'admission_id' => $admission->id,
        ])
        ->call('discharge')
        ->assertHasNoErrors();

    expect(App\Models\Patient::whereKey($patient->id)->exists())->toBeFalse();
    expect(Admission::whereKey($admission->id)->exists())->toBeFalse();
});
