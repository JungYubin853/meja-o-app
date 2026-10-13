<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'name',
        'floor_number',
        'grid_width',
        'grid_height',
        'grid_sections',
        'section_templates',
    ];

    public function getDisplayNameAttribute()
    {
        if (preg_match('/^(Lantai|Floor)\s+(\d+)$/i', $this->name, $matches)) {
            return __('Floor') . ' ' . $matches[2];
        }
        return $this->name;
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}