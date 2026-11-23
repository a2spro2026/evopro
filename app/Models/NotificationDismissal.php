<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationDismissal extends Model
{
    protected $fillable = [
        'user_key',
        'alert_key',
    ];
}
