<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    //

    const WORD_STATUS_PROPOSED = 0;
    const WORD_STATUS_APPROVED = 1;
    const WORD_STATUS_DISABLED = 2;

    public static $statuses = [
        0 => 'Черновик',
        1 => 'Опубликован',
        2 => 'Удален',
    ];

    protected $fillable = [
        'body', 'status', 'created_at', 'updated_at'
    ];

    protected $touches = ['word'];

    public function word()
    {
        return $this->belongsTo('App\Word');
    }
}
