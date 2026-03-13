<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    // ─── FILLABLE ──────────────────────────────────────
    // Columns that can be mass-assigned
    // Example: Company::create($request->all())
    // Only these columns will be saved
    // Protects against hackers sending extra fields
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'address',
        'city',
        'logo',
        'receipt_header',
        'receipt_footer',
        'plan',
        'status',
        'trial_ends_at',
        'currency',
        'timezone',
    ];

    // ─── CASTS ─────────────────────────────────────────
    // Tells Laravel how to convert column values
    // automatically when reading from database
    protected $casts = [
        'trial_ends_at' => 'datetime',  // string → Carbon date object
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    // ─── RELATIONSHIPS ─────────────────────────────────

    // A company HAS MANY users
    // Usage: $company->users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // A company HAS MANY categories
    // Usage: $company->categories
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // A company HAS MANY products
    // Usage: $company->products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // A company HAS MANY sales
    // Usage: $company->sales
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // A company HAS MANY subscriptions
    // Usage: $company->subscriptions
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // A company HAS ONE active subscription
    // Usage: $company->activeSubscription
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
                    ->where('status', 'active')
                    ->latest();
    }

    // ─── HELPER METHODS ────────────────────────────────

    // Check if company subscription is active
    // Usage: $company->isActive()
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Check if company is in trial
    // Usage: $company->isOnTrial()
    public function isOnTrial(): bool
    {
        return $this->status === 'trial' 
            && $this->trial_ends_at 
            && $this->trial_ends_at->isFuture();
    }

    // Check if trial has expired
    // Usage: $company->trialExpired()
    public function trialExpired(): bool
    {
        return $this->status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isPast();
    }
}