<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'outlet_id',
        'customer_name',
        'phone',
        'pax',
        'status',
        'created_by',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}