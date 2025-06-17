<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = ['name','key','description'];

    public function flows()
    {
        return $this->hasMany(Flow::class);
    }
}

