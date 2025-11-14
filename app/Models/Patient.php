<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [];

    public function getNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    /**
     * Get the admission of the patient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class);
    }
}
