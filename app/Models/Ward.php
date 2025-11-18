<?php

namespace App\Models;

use App\Models\Concerns\LogsActivityEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ward extends Model
{
    use LogsActivityEvents;

    /**
     * Get all of the admissions to the ward.
     */
    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * Get all of the patients in the ward.
     */
    public function patients(): HasManyThrough
    {
        return $this->hasManyThrough(
            Patient::class,
            Admission::class,
            'ward_id',
            'id',
            'id',
            'patient_id',
        );
    }

    /**
     * Returns the number of occupied beds in the ward.
     */
    public function occupiedBeds(): int
    {
        // If the count is already cached, return it.
        $count = $this->getAttribute('admissions_count');

        if ($count !== null) {
            return (int) $count;
        }

        // If the admissions relation is already loaded, use it.
        if ($this->relationLoaded('admissions')) {
            return $this->admissions->count();
        }

        // If the admissions relation is not loaded, query the database for the count of occupied beds.
        return $this->admissions()->count();
    }

    /**
     * Returns the number of free beds in the ward.
     *
     * The capacity of the ward minus the number of occupied beds.
     */
    public function freeBeds(): int
    {
        return max(($this->capacity ?? 0) - $this->occupiedBeds(), 0);
    }

    /**
     * Returns true if the ward has free beds.
     * The number of free beds is greater than 0.
     */
    public function hasFreeBeds(): bool
    {
        return $this->freeBeds() > 0;
    }
}
