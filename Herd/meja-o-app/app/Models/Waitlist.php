<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'customer_name',
        'phone',
        'pax',
        'status',
        'created_by', // <-- Add this line here!
    ];
}