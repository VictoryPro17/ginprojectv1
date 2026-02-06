<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'workshop_user')
            ->withPivot('status')
            ->withTimestamps();
    }
}
