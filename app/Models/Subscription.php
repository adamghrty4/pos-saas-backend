<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    // ─── FILLABLE ──────────────────────────────────────
    protected $fillable = [
        'company_id',
        'plan',
        'price',
        'currency',
        'starts_at',
        'ends_at',
        'status',
        'payment_reference',
        'payment_method',
        'paid_at',
    ];

    // ─── CASTS ─────────────────────────────────────────
    protected $casts = [
        'price'     => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'paid_at'   => 'datetime',
    ];

    // ─── RELATIONSHIPS ─────────────────────────────────

    // Subscription BELONGS TO a company
    // Usage: $subscription->company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // ─── SCOPES ────────────────────────────────────────

    // Only active subscriptions
    // Usage: Subscription::active()->get()
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('ends_at', '>', now());
    }

    // Subscriptions expiring soon (within 5 days)
    // Usage: Subscription::expiringSoon()->get()
    public function scopeExpiringSoon($query)
    {
        return $query->where('status', 'active')
                     ->whereBetween('ends_at', [now(), now()->addDays(5)]);
    }

    // ─── HELPER METHODS ────────────────────────────────

    // Check if subscription is currently valid
    // Usage: $subscription->isValid()
    public function isValid(): bool
    {
        return $this->status === 'active'
            && $this->ends_at->isFuture();
    }

    // How many days until expiry
    // Usage: $subscription->daysRemaining()
    public function daysRemaining(): int
    {
        return (int) now()->diffInDays($this->ends_at, false);
    }
}