<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'outlet_id',
        'table_number',
        'status',
        'capacity',
        'pax',
        'seated_time',
        'cleared_time',
        'shape',
        'grid_x',
        'grid_y',
        'width',
        'height',
        'created_by',
        'updated_by',
        'updated_by_user_id',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}