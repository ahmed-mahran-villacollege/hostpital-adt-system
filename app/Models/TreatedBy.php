<?php

namespace App\Models;

use App\Models\Concerns\LogsActivityEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatedBy extends Model
{
    use LogsActivityEvents;

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

    /**
     * Admission associated with this treatment.
     */
    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
