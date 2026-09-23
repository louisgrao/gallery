<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogVariant extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['attributes' => 'array'];
    }

    public function item()
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }
}