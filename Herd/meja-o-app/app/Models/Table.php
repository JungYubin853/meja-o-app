<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'table_number',
        'status',
        'capacity',
        'pax',
        'seated_time',
        'cleared_time',
        'updated_by_user_id',
    ];
}
