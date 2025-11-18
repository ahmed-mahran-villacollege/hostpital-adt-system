<?php

namespace App\Models;

use App\Models\Concerns\LogsActivityEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Team extends Model
{
    use LogsActivityEvents;

    /**
     * Returns a title for the team, prefixed with "Team: ".
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        return 'Team: '.$this->name;
    }

    /**
     * Consultant responsible for the team.
     */
    public function consultant()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Doctors assigned to the team.
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'team_members');
    }

    /**
     * Get all of the teamMembers.
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get all of the patients assigned to the team.
     */
    public function patients(): HasManyThrough
    {
        return $this->hasManyThrough(
            Patient::class,
            Admission::class,
            'team_id',
            'id',
            'id',
            'patient_id'
        );
    }
}
