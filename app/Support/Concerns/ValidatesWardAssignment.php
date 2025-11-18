<?php

namespace App\Support\Concerns;

use App\Models\Ward;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

trait ValidatesWardAssignment
{
    /**
     * Validate the ward assignment with the given parameters.
     *
     * @throws ValidationException
     */
    protected function validateWardAssignment(
        ?int $wardId,
        ?string $patientSex,
        string $wardAttribute = 'ward_id',
        bool $ignoreCapacity = false,
        ?string $sexAttribute = 'patient.sex',
    ): Ward {
        $ward = Ward::query()
            ->withCount('admissions')
            ->find($wardId);

        if (! $ward) {
            $this->wardValidationFailure('Select a valid ward.', $wardAttribute);
        }

        if (blank($patientSex)) {
            $this->wardValidationFailure(
                'Please confirm the patient sex before assigning a ward.',
                $sexAttribute ?? $wardAttribute,
            );
        }

        if ($ward->type !== $patientSex) {
            $this->wardValidationFailure(
                'The ward type must match with the patient.',
                $wardAttribute,
            );
        }

        if (! $ignoreCapacity && ! $ward->hasFreeBeds()) {
            $this->wardValidationFailure(
                'The selected ward has no free beds available.',
                $wardAttribute,
            );
        }

        return $ward;
    }

    /**
     * Throw a validation exception with a notification about a ward assignment failure.
     *
     * @param  string  $message  The error message to display.
     * @param  string  $attribute  The attribute to which the error message should be associated.
     *
     * @throws ValidationException
     */
    protected function wardValidationFailure(string $message, string $attribute): never
    {
        Notification::make()
            ->title('Ward assignment blocked')
            ->body($message)
            ->danger()
            ->send();

        throw ValidationException::withMessages([
            $attribute => $message,
        ]);
    }
}
