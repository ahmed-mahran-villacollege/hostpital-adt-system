<?php

use App\Models\Doctor;
use App\Models\Team;
use App\Models\Ward;
use Illuminate\Support\Str;
use Livewire\Livewire;
use App\Filament\Resources\Teams\Pages\CreateTeam;

it('creates a ward with a type and fixed capacity', function () {
    $ward = Ward::create([
        'name' => 'Ward Male '.Str::random(4),
        'type' => 'Male',
        'capacity' => 10,
    ]);

    expect($ward->type)->toBe('Male');
    expect($ward->capacity)->toBe(10);
    expect($ward->hasFreeBeds())->toBeTrue();
});

it('creates a team with a consultant and at least one grade 1 junior doctor', function () {
    $consultant = Doctor::factory()->create(['rank' => 'Consultant', 'grade' => 5]);
    $junior = Doctor::factory()->create(['rank' => 'Junior', 'grade' => 1]);

    $team = Team::create([
        'name' => 'Team '.Str::random(4),
        'code' => Str::upper(Str::random(3)),
        'consultant_id' => $consultant->id,
    ]);

    $team->doctors()->attach($junior->id);

    expect($team->consultant?->id)->toBe($consultant->id);
    expect($team->doctors()->where('rank', 'Junior')->where('grade', 1)->exists())->toBeTrue();
});

it('shows an error when creating a team without a grade 1 junior doctor', function () {
    $user = testUserWithPermissions('team.create');
    $consultant = Doctor::factory()->create(['rank' => 'Consultant', 'grade' => 5]);
    $junior = Doctor::factory()->create(['rank' => 'Junior', 'grade' => 2]);

    Livewire::actingAs($user)
        ->test(CreateTeam::class)
        ->set('data', [
            'name' => 'Team '.Str::random(4),
            'code' => Str::upper(Str::random(3)),
            'consultant_id' => $consultant->id,
            'teamMembers' => [
                ['doctor_id' => $junior->id],
            ],
        ])
        ->call('create')
        ->assertHasErrors(['teamMembers']);

    expect(Team::count())->toBe(0);
});
