<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    //

    public static $statuses = [
        0 => 'Черновик',
        1 => 'Активно',
        2 => 'Удалено',
    ];

    const ANNOUNCEMENT_STATUS_PROPOSED = 0;
    const ANNOUNCEMENT_STATUS_APPROVED = 1;
    const ANNOUNCEMENT_STATUS_DISABLED = 2;



}
