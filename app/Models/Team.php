<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
