<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the admission of the patient.
     */
    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class);
    }

    /**
     * Get all of the treatedBy details for the patient.
     */
    public function treatedBy(): HasManyThrough
    {
        return $this->hasManyThrough(TreatedBy::class, Admission::class);
    }
}
