<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatedBy extends Model
{
    protected $table = 'treated_by';

    protected $fillable = [
        'admission_id',
        'doctor_id',
        'treated_at',
    ];

    /**
     * Get the doctor who treated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
