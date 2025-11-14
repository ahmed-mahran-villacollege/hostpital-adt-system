<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    public function consultant()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'team_members');
    }

    /**
     * Get all of the teamMembers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }
}
