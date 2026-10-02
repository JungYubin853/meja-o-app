<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'outlet_id',
        'table_id',
        'customer_name',
        'phone',
        'pax',
        'started_at',
        'ended_at',
        'time_elapsed',
        'created_by',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}