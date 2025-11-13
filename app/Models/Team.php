<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function consultant()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function junior()
    {
        return $this->belongsTo(Doctor::class);
    }
}
