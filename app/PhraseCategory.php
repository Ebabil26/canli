<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhraseCategory extends Model
{
    //
    protected $fillable = [
        'name', 'icon'
    ];

    /**
     * @return array
     */
    public function phrases()
    {
        return $this->hasMany('App\Phrase', 'phrase_category_id');
    }
}
