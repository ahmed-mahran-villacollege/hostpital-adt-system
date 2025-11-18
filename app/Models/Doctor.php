<?php

namespace App\Models;

use App\Models\Concerns\LogsActivityEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorFactory> */
    use HasFactory;
    use LogsActivityEvents;

    /**
     * Teams this doctor belongs to.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members');
    }
}
