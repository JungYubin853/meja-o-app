<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'outlet_id', // Changed from brand
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function getDefaultPermissions()
    {
        if ($this->isSuperAdmin()) {
            return []; // Super Admin has access to everything
        }

        return [
            'nav_dashboard' => true,
            'dash_create_table' => $this->isAdmin(),
            'dash_floor_canvas' => true,
            'dash_guest_seating' => true,
            'dash_waitlist' => true,
            'dash_edit_table' => $this->isAdmin(),
            'nav_waitlist' => true,
            'nav_reports' => true,
            'rep_overall' => $this->isAdmin(),
            'rep_daily' => true,
            'rep_monthly' => $this->isAdmin(),
            'rep_yearly' => $this->isAdmin(),
            'rep_waitlist' => true,
            'rep_habits' => $this->isAdmin(),
            'nav_profile' => true,
            'nav_account_mgmt' => $this->isAdmin(),
            'acc_create_account' => false,
            'acc_user_list' => $this->isAdmin(),
            'nav_role_permission' => false,
        ];
    }

    public function hasPermission($key)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $perms = $this->permissions ?? [];
        if (array_key_exists($key, $perms)) {
            return (bool) $perms[$key];
        }

        $defaults = $this->getDefaultPermissions();
        return $defaults[$key] ?? false;
    }
}
