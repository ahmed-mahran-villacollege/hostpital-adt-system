<?php

use App\Support\Concerns\ValidatesWardAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(Tests\TestCase::class, RefreshDatabase::class);

trait ValidatesWardAssignmentStub
{
    use ValidatesWardAssignment;
}

beforeEach(function () {
    $this->validator = new class
    {
        use ValidatesWardAssignmentStub;

        public function runValidation(...$args)
        {
            return $this->validateWardAssignment(...$args);
        }
    };
});

it('allows matching ward type and available capacity', function () {
    $ward = testWard('Male', 1);

    $result = $this->validator->runValidation(
        wardId: $ward->id,
        patientSex: 'Male',
    );

    expect($result->id)->toBe($ward->id);
});

it('blocks when ward type mismatches patient', function () {
    $ward = testWard('Female', 1);

    $this->expectException(ValidationException::class);

    $this->validator->runValidation(
        wardId: $ward->id,
        patientSex: 'Male',
    );
});

it('blocks when ward has no free beds', function () {
    $ward = testWard('Male', 0);

    $this->expectException(ValidationException::class);

    $this->validator->runValidation(
        wardId: $ward->id,
        patientSex: 'Male',
    );
});

it('allows capacity check to be ignored when flagged', function () {
    $ward = testWard('Male', 0);

    $result = $this->validator->runValidation(
        wardId: $ward->id,
        patientSex: 'Male',
        ignoreCapacity: true,
    );

    expect($result->id)->toBe($ward->id);
});
