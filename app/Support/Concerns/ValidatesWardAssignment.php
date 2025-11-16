<?php

namespace App\Support\Concerns;

use App\Models\Ward;
use Illuminate\Validation\ValidationException;

trait ValidatesWardAssignment
{
    /**
     * @throws ValidationException
     */
    protected function validateWardAssignment(
        ?int $wardId,
        ?string $patientSex,
        string $attribute = 'ward_id',
        bool $ignoreCapacity = false,
    ): Ward {
        $ward = Ward::query()
            ->withCount('admissions')
            ->find($wardId);

        if (! $ward) {
            throw ValidationException::withMessages([
                $attribute => 'Select a valid ward.',
            ]);
        }

        if (blank($patientSex)) {
            throw ValidationException::withMessages([
                $attribute => 'Please confirm the patient sex before assigning a ward.',
            ]);
        }

        if ($ward->type !== $patientSex) {
            throw ValidationException::withMessages([
                $attribute => 'The ward type must match the patient sex.',
            ]);
        }

        if (! $ignoreCapacity && ! $ward->hasFreeBeds()) {
            throw ValidationException::withMessages([
                $attribute => 'The selected ward has no free beds available.',
            ]);
        }

        return $ward;
    }
}
