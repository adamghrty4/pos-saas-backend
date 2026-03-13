<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens,   // gives user ability to have tokens (Sanctum)
        HasFactory,
        Notifiable,
        SoftDeletes,
        HasRoles;       // gives user roles and permissions (Spatie)

    // ─── FILLABLE ──────────────────────────────────────
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'password',
        'pin_code',
        'pin_active',
        'role',
        'is_active',
        'last_login_at',
    ];

    // ─── HIDDEN ────────────────────────────────────────
    // These columns are NEVER returned in API responses
    // Even if you return the full user object
    // password and remember_token stay hidden always
    protected $hidden = [
        'password',
        'remember_token',
        'pin_code',       // never expose PIN in API response
    ];

    // ─── CASTS ─────────────────────────────────────────
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'pin_active'        => 'boolean',
        'is_active'         => 'boolean',
        'password'          => 'hashed',  // auto-hashes password on save
    ];

    // ─── RELATIONSHIPS ─────────────────────────────────

    // User BELONGS TO a company
    // Usage: $user->company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // User HAS MANY sales (as waiter)
    // Usage: $user->sales
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // User HAS MANY stock movements
    // Usage: $user->stockMovements
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─── HELPER METHODS ────────────────────────────────

    // Check if user can access the system
    // Usage: $user->canLogin()
    public function canLogin(): bool
    {
        return $this->is_active === true;
    }

    // Check if user is owner or manager
    // Usage: $user->isManager()
    public function isManager(): bool
    {
        return in_array($this->role, ['owner', 'manager', 'super_admin']);
    }

    // Check if user is a waiter
    // Usage: $user->isWaiter()
    public function isWaiter(): bool
    {
        return $this->role === 'waiter';
    }

    // Check if user is super admin (you)
    // Usage: $user->isSuperAdmin()
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}