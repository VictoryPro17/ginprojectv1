<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    protected $casts = [
        'dob' => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function caretaker()
    {
        return $this->belongsTo(User::class, 'caretaker_id');
    }
}
