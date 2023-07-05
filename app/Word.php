<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{

    protected $fillable = [
        'word',
        'word_formatted',
        'word_latin',
        'word_latin_formatted',
        'translation',
        'language_id',
    ];

    protected $hidden = [
      //'created_at',
      //'updated_at',
      //'status'
    ];

    const WORD_STATUS_PROPOSED = 0;
    const WORD_STATUS_APPROVED = 1;
    const WORD_STATUS_DISABLED = 2;

    public static $statuses = [
        0 => 'Черновик',
        1 => 'Опубликован',
        2 => 'Удален',
    ];

    public function translations()
    {
        return $this->hasMany('App\Translation');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function language()
    {
        return $this->belongsTo('App\Language');
    }
}
