<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transition extends Model
{
    protected $fillable = ['flow_id','from_activity_id','to_activity_id','condition'];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function from()
    {
        return $this->belongsTo(Activity::class, 'from_activity_id');
    }

    public function to()
    {
        return $this->belongsTo(Activity::class, 'to_activity_id');
    }
}

