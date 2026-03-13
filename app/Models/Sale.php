<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    // ─── FILLABLE ──────────────────────────────────────
    protected $fillable = [
        'company_id',
        'user_id',
        'reference',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'payment_method',
        'cash_received',
        'change_given',
        'status',
        'note',
        'table_number',
    ];

    // ─── CASTS ─────────────────────────────────────────
    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'total'           => 'decimal:2',
        'cash_received'   => 'decimal:2',
        'change_given'    => 'decimal:2',
        'created_at'      => 'datetime',
    ];

    // ─── RELATIONSHIPS ─────────────────────────────────

    // Sale BELONGS TO a company
    // Usage: $sale->company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Sale BELONGS TO a user (the waiter)
    // Usage: $sale->waiter
    public function waiter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Sale HAS MANY items
    // Usage: $sale->items
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Sale HAS MANY stock movements
    // Usage: $sale->stockMovements
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─── SCOPES ────────────────────────────────────────

    // Only today's sales
    // Usage: Sale::today()->get()
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Sales in a date range
    // Usage: Sale::inPeriod($from, $to)->get()
    public function scopeInPeriod($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    // Only completed sales
    // Usage: Sale::completed()->get()
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ─── HELPER METHODS ────────────────────────────────

    // Generate a unique reference number
    // Usage: Sale::generateReference($companySlug)
    public static function generateReference(string $companySlug): string
    {
        $prefix = strtoupper(substr($companySlug, 0, 3));
        $date   = now()->format('Ymd');
        $count  = static::whereDate('created_at', today())->count() + 1;
        return $prefix . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}