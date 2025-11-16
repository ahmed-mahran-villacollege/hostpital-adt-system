<?php

namespace App\Support\Concerns;

use App\Models\Doctor;
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

        $hasGradeOneJunior = Doctor::query()
            ->whereIn('id', $doctorIds)
            ->where('rank', 'Junior')
            ->where('grade', 1)
            ->exists();

        if ($hasGradeOneJunior) {
            return;
        }

        throw ValidationException::withMessages([
            'teamMembers' => 'Each team must include at least one Grade 1 junior doctor.',
        ]);
    }
}
