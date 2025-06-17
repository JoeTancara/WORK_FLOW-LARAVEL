<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['flow_id','name','type','config'];

    protected $casts = [
        'config' => 'array',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function outgoing()
    {
        return $this->hasMany(Transition::class, 'from_activity_id');
    }

    public function incoming()
    {
        return $this->hasMany(Transition::class, 'to_activity_id');
    }
}

