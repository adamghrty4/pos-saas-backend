<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    // ─── FILLABLE ──────────────────────────────────────
    protected $fillable = [
        'company_id',
        'product_id',
        'user_id',
        'sale_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'note',
    ];

    // ─── CASTS ─────────────────────────────────────────
    protected $casts = [
        'quantity'    => 'integer',
        'stock_before'=> 'integer',
        'stock_after' => 'integer',
    ];

    // ─── RELATIONSHIPS ─────────────────────────────────

    // StockMovement BELONGS TO a company
    // Usage: $movement->company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // StockMovement BELONGS TO a product
    // Usage: $movement->product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // StockMovement BELONGS TO a user (who made the change)
    // Usage: $movement->user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // StockMovement BELONGS TO a sale (if caused by sale)
    // Usage: $movement->sale
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // ─── SCOPES ────────────────────────────────────────

    // Only sale-type movements
    // Usage: StockMovement::fromSales()->get()
    public function scopeFromSales($query)
    {
        return $query->where('type', 'sale');
    }

    // Only manual movements
    // Usage: StockMovement::manual()->get()
    public function scopeManual($query)
    {
        return $query->whereIn('type', [
            'manual_add',
            'manual_remove',
            'adjustment'
        ]);
    }
}