<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['language', 'code'];

    public function words()
    {
        return $this->hasMany('App\Word');
    }
}


