<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $table = 'user_settings';

    protected $fillable = [
        'user_id',
        'delivery_lead_days',
    ];

    protected $casts = [
        'delivery_lead_days' => 'integer',
    ];
}