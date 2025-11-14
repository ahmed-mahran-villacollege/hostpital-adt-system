<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    protected $casts = [
        'admitted_at' => 'date',
    ];

    /**
     * Get the admitted patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the admission ward.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the team assigned for the admission.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
