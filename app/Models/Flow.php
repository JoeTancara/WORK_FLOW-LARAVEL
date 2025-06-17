<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    protected $fillable = ['process_id','version','active'];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function transitions()
    {
        return $this->hasMany(Transition::class);
    }
}

