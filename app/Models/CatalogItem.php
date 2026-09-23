<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    protected $guarded = [];

    public function variants()
    {
        return $this->hasMany(CatalogVariant::class);
    }

    public function media()
    {
        return $this->belongsToMany(CatalogMedia::class)->withPivot('position')->orderBy('position');
    }

    public function accessories()
    {
        return $this->belongsToMany(CatalogAccessory::class);
    }
}