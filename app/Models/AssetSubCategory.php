<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetSubCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'asset_category_id',
    ];

    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function assetSubSubCategories()
    {
        return $this->hasMany(AssetSubSubCategory::class);
    }
}
