<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    /** @use HasFactory<\Database\Factories\TeamMemberFactory> */
    use HasFactory;

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
