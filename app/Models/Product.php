<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // ─── FILLABLE ──────────────────────────────────────
    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'description',
        'image',
        'barcode',
        'price',
        'cost_price',
        'stock_quantity',
        'min_stock_alert',
        'track_stock',
        'is_active',
        'is_available',
        'sort_order',
    ];

    // ─── CASTS ─────────────────────────────────────────
    protected $casts = [
        'price'           => 'decimal:2',
        'cost_price'      => 'decimal:2',
        'stock_quantity'  => 'integer',
        'min_stock_alert' => 'integer',
        'track_stock'     => 'boolean',
        'is_active'       => 'boolean',
        'is_available'    => 'boolean',
        'sort_order'      => 'integer',
    ];

    // ─── RELATIONSHIPS ─────────────────────────────────

    // Product BELONGS TO a company
    // Usage: $product->company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Product BELONGS TO a category
    // Usage: $product->category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Product HAS MANY sale items
    // Usage: $product->saleItems
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Product HAS MANY stock movements
    // Usage: $product->stockMovements
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─── SCOPES ────────────────────────────────────────

    // Only active products
    // Usage: Product::active()->get()
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Only available products (can be sold now)
    // Usage: Product::available()->get()
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                     ->where('is_active', true);
    }

    // Products below minimum stock
    // Usage: Product::lowStock()->get()
    public function scopeLowStock($query)
    {
        return $query->where('track_stock', true)
                     ->whereColumn('stock_quantity', '<=', 'min_stock_alert');
    }

    // ─── HELPER METHODS ────────────────────────────────

    // Check if product is low on stock
    // Usage: $product->isLowStock()
    public function isLowStock(): bool
    {
        return $this->track_stock 
            && $this->stock_quantity <= $this->min_stock_alert;
    }

    // Calculate profit margin
    // Usage: $product->profitMargin()
    public function profitMargin(): ?float
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return null;
        }
        return round($this->price - $this->cost_price, 2);
    }
}