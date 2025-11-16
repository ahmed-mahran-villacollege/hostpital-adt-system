<?php

namespace App\Support\Concerns;

use App\Models\Ward;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

trait ValidatesWardAssignment
{
    /**
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
