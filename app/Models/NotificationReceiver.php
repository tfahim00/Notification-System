<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationReceiver extends Model
{
    protected $fillable = [
        'username',
        'email',
        'position',
    ];
}
