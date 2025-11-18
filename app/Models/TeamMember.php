<?php

namespace App\Models;

use App\Models\Concerns\LogsActivityEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    /** @use HasFactory<\Database\Factories\TeamMemberFactory> */
    use HasFactory;
    use LogsActivityEvents;

    protected $fillable = [
        'team_id',
        'doctor_id',
    ];

    /**
     * Team for this membership.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Doctor assigned through this membership.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
