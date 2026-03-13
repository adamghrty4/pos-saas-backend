<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    // ─── FILLABLE ──────────────────────────────────────
    protected $fillable = [
        'sale_id',
        'product_id',
        'company_id',
        'product_name',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    // ─── CASTS ─────────────────────────────────────────
    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    // ─── RELATIONSHIPS ─────────────────────────────────

    // SaleItem BELONGS TO a sale
    // Usage: $item->sale
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // SaleItem BELONGS TO a product
    // Usage: $item->product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // SaleItem BELONGS TO a company
    // Usage: $item->company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}