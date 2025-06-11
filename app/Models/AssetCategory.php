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

    public function assetSubSubCategories()
    {
        return $this->hasMany(AssetSubSubCategory::class, 'asset_category_id');
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
