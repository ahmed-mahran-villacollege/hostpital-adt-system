<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [];

    /**
     * Get the admission of the patient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class);
    }

    /**
     * Get all of the treatedBy details for the patient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function treatedBy(): HasManyThrough
    {
        return $this->hasManyThrough(TreatedBy::class, Admission::class);
    }
}
