<?php

use App\Support\Concerns\ValidatesWardAssignment;
use App\Models\Ward;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

trait ValidatesWardAssignmentStub
{
    use ValidatesWardAssignment;
}

beforeEach(function () {
    $this->validator = new class {
        use ValidatesWardAssignmentStub;

        public function runValidation(...$args)
        {
            return $this->validateWardAssignment(...$args);
        }
    };
});

function wardForTest(string $type, int $capacity = 1): Ward
{
    return Ward::create([
        'name' => 'Ward '.$type.' '.Str::random(4),
        'type' => $type,
        'capacity' => $capacity,
    ]);
}

it('allows matching ward type and available capacity', function () {
    $ward = wardForTest('Male', 1);

    $result = $this->validator->runValidation(
        wardId: $ward->id,
        patientSex: 'Male',
    );

    expect($result->id)->toBe($ward->id);
});

it('blocks when ward type mismatches patient sex', function () {
    $ward = wardForTest('Female', 1);

    $this->expectException(ValidationException::class);

    $this->validator->runValidation(
        wardId: $ward->id,
        patientSex: 'Male',
    );
});

it('blocks when ward has no free beds', function () {
    $ward = wardForTest('Male', 0);

    $this->expectException(ValidationException::class);

    $this->validator->runValidation(
        wardId: $ward->id,
        patientSex: 'Male',
    );
});

it('allows capacity check to be ignored when flagged', function () {
    $ward = wardForTest('Male', 0);

    $result = $this->validator->runValidation(
        wardId: $ward->id,
        patientSex: 'Male',
        ignoreCapacity: true,
    );

    expect($result->id)->toBe($ward->id);
});
