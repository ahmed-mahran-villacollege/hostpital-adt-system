<?php

namespace App\Support;

use App\Models\Ward;

class WardCapacityFormatter
{
    /**
     * Get the ward occupancy for the provided ward ID.
     *
     * @param int|null $wardId
     * @return string
     */
    public static function forWardId(?int $wardId): string
    {
        if (! $wardId) {
            return 'Select a ward to view occupancy.';
        }

        $ward = Ward::query()
            ->withCount('admissions')
            ->find($wardId);

        if (! $ward) {
            return 'Ward not found.';
        }

        return static::forWard($ward);
    }

    /**
     * Get the ward occupancy as a string.
     *
     * @param Ward|null $ward
     * @return string
     *
     */
    public static function forWard(?Ward $ward): string
    {
        if (! $ward) {
            return 'Select a ward to view occupancy.';
        }

        $occupied = $ward->occupiedBeds();
        $capacity = (int) ($ward->capacity ?? 0);
        $free = $ward->freeBeds();

        return "{$occupied} / {$capacity} beds occupied ({$free} free)";
    }
}
