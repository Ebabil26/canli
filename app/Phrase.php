<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phrase extends Model
{
    protected $fillable = [
        'phrase', 'translation', 'phrase_category_id'
    ];

    public function category()
    {
        return $this->belongsTo('App\PhraseCategory', 'phrase_category_id');
    }
}
