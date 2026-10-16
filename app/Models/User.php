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
        'permissions',
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

    public function getHardcodedDefaults()
    {
        if ($this->isSuperAdmin()) {
            return [];
        }

        return [
            'nav_dashboard' => true,
            'dash_create_table' => $this->isAdmin(),
            'dash_create_section' => $this->isAdmin(),
            'dash_floor_canvas' => true,
            'dash_view_mode' => true,
            'dash_floor_dropdown' => true,
            'dash_canvas_settings' => $this->isAdmin(),
            'dash_section_edit' => $this->isAdmin(),
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
            'nav_tutorial' => true,
            'nav_calendar' => true,
            'nav_profile' => true,
            'nav_account_mgmt' => $this->isAdmin(),
            'acc_create_account' => false,
            'acc_user_list' => $this->isAdmin(),
            'nav_role_permission' => false,
        ];
    }

    public function getDefaultPermissions()
    {
        if ($this->isSuperAdmin()) {
            return []; // Super Admin has access to everything
        }

        try {
            $superAdmin = static::where('role', 'super_admin')->first();
            if ($superAdmin && !empty($superAdmin->permissions)) {
                $saved = is_string($superAdmin->permissions) ? json_decode($superAdmin->permissions, true) : (array)$superAdmin->permissions;
                
                if ($this->role === 'staff' && isset($saved['_staff_defaults'])) {
                    return array_merge($this->getHardcodedDefaults(), (array)$saved['_staff_defaults']);
                }
                if ($this->role === 'admin' && isset($saved['_admin_defaults'])) {
                    return array_merge($this->getHardcodedDefaults(), (array)$saved['_admin_defaults']);
                }
            }
        } catch (\Exception $e) {
            // Fallback in case of DB issues during migration/setup
        }

        return $this->getHardcodedDefaults();
    }

    public function hasPermission($key)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $perms = $this->permissions;
        if (is_string($perms)) {
            $perms = json_decode($perms, true) ?: [];
        }
        $perms = (array) $perms;

        if (array_key_exists($key, $perms)) {
            return (bool) $perms[$key];
        }

        $defaults = $this->getDefaultPermissions();
        return (bool) ($defaults[$key] ?? false);
    }
}
