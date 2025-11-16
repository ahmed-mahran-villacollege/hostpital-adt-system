<?php

namespace App\Support\Concerns;

use App\Models\Doctor;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

trait ValidatesTeamComposition
{
    /**
     * @param  array<int, array<string, mixed>>  $teamMembers
     *
     * @throws ValidationException
     */
    protected function validateTeamMembers(array $teamMembers): void
    {
        $doctorIds = collect($teamMembers)
            ->pluck('doctor_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($doctorIds)) {
            $this->notifyTeamValidationFailure('The team members field must have at least 1 doctor.');
        }

        $hasGradeOneJunior = Doctor::query()
            ->whereIn('id', $doctorIds)
            ->where('rank', 'Junior')
            ->where('grade', 1)
            ->exists();

        if ($hasGradeOneJunior) {
            return;
        }

        $this->notifyTeamValidationFailure('Each team must include at least one Grade 1 junior doctor.');
    }

    protected function notifyTeamValidationFailure(string $message): never
    {
        Notification::make()
            ->danger()
            ->title('Team validation failed')
            ->body($message)
            ->send();

        throw ValidationException::withMessages([
            'teamMembers' => $message,
        ]);
    }
}
