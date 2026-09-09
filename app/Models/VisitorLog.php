<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'customer_name',
        'phone',
        'pax',
        'started_at',
        'ended_at',
        'time_elapsed',
        'created_by',
    ];
}