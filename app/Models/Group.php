<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
