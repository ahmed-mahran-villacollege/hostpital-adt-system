<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admission extends Model
{
    protected $casts = [
        'admitted_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleted(function (Admission $admission) {
            $admission->patient()->delete();
        });
    }

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

    /**
     * Get all of the treatedBy details for the admission.
     */
    public function treatedBy(): HasMany
    {
        return $this->hasMany(TreatedBy::class);
    }
}
