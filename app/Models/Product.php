<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $guarded = [];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class);
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(ProductMedia::class)
                    ->withPivot('position')
                    ->orderBy('pivot_position', 'asc');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(ProductDownload::class)->orderBy('position', 'asc');
    }
}