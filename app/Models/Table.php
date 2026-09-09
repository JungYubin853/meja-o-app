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
        'created_by',         // <-- Add this to track who created/generated the table
        'updated_by',         // <-- Add this to track who last updated/seated the table
        'updated_by_user_id',
    ];
}