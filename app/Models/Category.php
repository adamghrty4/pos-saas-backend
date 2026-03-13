<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    // ─── FILLABLE ──────────────────────────────────────
    protected $fillable = [
        'company_id',
        'name',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    // ─── CASTS ─────────────────────────────────────────
    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ─── RELATIONSHIPS ─────────────────────────────────

    // Category BELONGS TO a company
    // Usage: $category->company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Category HAS MANY products
    // Usage: $category->products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // ─── SCOPES ────────────────────────────────────────
    // Scopes = reusable query filters
    // Usage: Category::active()->get()
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Usage: Category::ordered()->get()
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}