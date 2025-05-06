<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
    ];

    public function assetSubCategories()
    {
        return $this->hasMany(AssetSubCategory::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
