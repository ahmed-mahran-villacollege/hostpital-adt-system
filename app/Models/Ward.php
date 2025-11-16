<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ward extends Model
{
    protected $fillable = [
        'name',
        'type',
        'capacity',
    ];

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
}
