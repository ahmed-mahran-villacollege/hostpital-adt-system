<?php

use App\Filament\Pages\RecordTreatedBy;
use App\Models\Admission;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\TreatedBy;
use Livewire\Livewire;

it('records treatment for a doctor on the admission team', function () {
    $user = testUserWithPermissions('patient.record_treatment');
    $ward = testWard('Male');
    $team = testTeam();
    $teamDoctor = Doctor::factory()->create(['rank' => 'Junior', 'grade' => 2]);
    $team->doctors()->attach($teamDoctor->id);
    $patient = Patient::factory()->create(['sex' => 'Male']);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'ward_id' => $ward->id,
        'team_id' => $team->id,
    ]);

    Livewire::actingAs($user)
        ->test(RecordTreatedBy::class)
        ->set('data', [
            'admission_id' => $admission->id,
            'doctor_id' => $teamDoctor->id,
        ])
        ->call('recordTreatment')
        ->assertHasNoErrors();

    expect(
        TreatedBy::where('admission_id', $admission->id)
            ->where('doctor_id', $teamDoctor->id)
            ->exists()
    )->toBeTrue();
});

it('blocks recording treatment for a doctor outside the admission team', function () {
    $user = testUserWithPermissions('patient.record_treatment');
    $ward = testWard('Male');
    $team = testTeam();
    $teamDoctor = Doctor::factory()->create(['rank' => 'Junior', 'grade' => 2]);
    $team->doctors()->attach($teamDoctor->id);
    $otherDoctor = Doctor::factory()->create(['rank' => 'Junior', 'grade' => 2]);
    $patient = Patient::factory()->create(['sex' => 'Male']);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'ward_id' => $ward->id,
        'team_id' => $team->id,
    ]);

    Livewire::actingAs($user)
        ->test(RecordTreatedBy::class)
        ->set('data', [
            'admission_id' => $admission->id,
            'doctor_id' => $otherDoctor->id,
        ])
        ->call('recordTreatment')
        ->assertHasErrors();

    expect(
        TreatedBy::where('admission_id', $admission->id)
            ->where('doctor_id', $otherDoctor->id)
            ->exists()
    )->toBeFalse();
});
