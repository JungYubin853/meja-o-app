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
        'viewable_password',
        'role',
        'status',
        'is_self_registered',
        'outlet_id', // Changed from brand
        'permissions',
    ];

    public function getViewablePassword(): ?string
    {
        if (empty($this->viewable_password)) {
            return null;
        }
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($this->viewable_password);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function passwordResetRequests()
    {
        return $this->hasMany(PasswordResetRequest::class);
    }
    
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved' || $this->isSuperAdmin();
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
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
            'is_self_registered' => 'boolean',
        ];
    }

    public function getHardcodedDefaults()
    {
        if ($this->isSuperAdmin()) {
            return [];
        }

        return [
            'nav_dashboard' => true,
            'dash_view_mode' => true,
            'dash_table_edit' => $this->isAdmin(),
            'dash_section_edit' => $this->isAdmin(),
            'dash_create_table' => $this->isAdmin(),
            'dash_create_section' => $this->isAdmin(),
            'dash_canvas_settings' => $this->isAdmin(),
            'dash_floor_dropdown' => true,
            'dash_floor_canvas' => true,
            'dash_guest_seating' => true,
            'dash_waitlist' => true,
            'dash_edit_table' => $this->isAdmin(),
            'nav_waitlist' => true,
            'nav_reports' => true,
            'rep_overall' => $this->isAdmin(),
            'rep_daily' => true,
            'rep_weekly' => true,
            'rep_monthly' => $this->isAdmin(),
            'rep_yearly' => $this->isAdmin(),
            'rep_waitlist' => true,
            'rep_habits' => $this->isAdmin(),
            'nav_tutorial' => true,
            'tut_admin_guide' => $this->isAdmin(),
            'tut_staff_guide' => true,
            'nav_calendar' => true,
            'nav_profile' => true,
            'prof_search_bar' => $this->isAdmin(),
            'prof_account_info' => true,
            'prof_password_requests' => $this->isAdmin(),
            'prof_table_settings' => $this->isAdmin(),
            'prof_col_user' => $this->isAdmin(),
            'prof_col_outlet' => $this->isAdmin(),
            'prof_col_role' => $this->isAdmin(),
            'prof_col_submitted' => $this->isAdmin(),
            'prof_col_password' => $this->isAdmin(),
            'prof_col_actions' => $this->isAdmin(),
            'nav_account_mgmt' => $this->isAdmin(),
            'acc_search_bar' => true,
            'acc_create_account' => false,
            'acc_user_list' => $this->isAdmin(),
            'acc_table_settings' => true,
            'acc_col_user' => true,
            'acc_col_outlet' => true,
            'acc_col_role' => true,
            'acc_col_source' => true,
            'acc_col_status' => true,
            'acc_col_submitted' => true,
            'acc_col_actions' => true,
            'nav_role_permission' => false,
            'nav_approval' => $this->isAdmin(),
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
        $perms = $this->permissions;
        if (is_string($perms)) {
            $perms = json_decode($perms, true) ?: [];
        }
        $perms = (array) $perms;

        // Custom column visibility preferences: if set by user, respect even for super admin
        if ((str_starts_with($key, 'acc_col_') || str_starts_with($key, 'prof_col_')) && array_key_exists($key, $perms)) {
            return (bool) $perms[$key];
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        if (array_key_exists($key, $perms)) {
            return (bool) $perms[$key];
        }

        $defaults = $this->getDefaultPermissions();
        return (bool) ($defaults[$key] ?? false);
    }
}
