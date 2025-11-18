<?php

use App\Filament\Pages\DischargePatient;
use App\Filament\Pages\TransferPatient;
use App\Filament\Resources\Admissions\Pages\CreateAdmission;
use App\Models\Admission;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Team;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

function userWithPermission(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }

    $user->givePermissionTo($permissions);

    return $user;
}

function ward(string $type = 'Male', int $capacity = 1): Ward
{
    return Ward::create([
        'name' => 'Ward '.$type.' '.Str::random(4),
        'type' => $type,
        'capacity' => $capacity,
    ]);
}

function team(): Team
{
    $consultant = Doctor::factory()->create(['rank' => 'Consultant', 'grade' => 5]);

    return Team::create([
        'name' => 'Team '.Str::random(4),
        'code' => Str::upper(Str::random(3)),
        'consultant_id' => $consultant->id,
    ]);
}

it('creates an admission with a new patient and validates ward assignment', function () {
    $user = userWithPermission('patient.admit');
    $ward = ward('Female');
    $team = team();

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
    $user = userWithPermission('patient.transfer');
    $maleWard = ward('Male');
    $femaleWard = ward('Female');
    $team = team();

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
    $user = userWithPermission('patient.transfer');
    $maleWard = ward('Male');
    $newWard = ward('Male');
    $team = team();
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
    $user = userWithPermission('patient.discharge');
    $ward = ward('Male');
    $team = team();
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
