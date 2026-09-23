<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogAccessory extends Model
{
    protected $guarded = [];

    public function children()
    {
        return $this->hasMany(CatalogAccessory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(CatalogAccessory::class, 'parent_id');
    }

    public function items()
    {
        return $this->belongsToMany(CatalogItem::class);
    }
}