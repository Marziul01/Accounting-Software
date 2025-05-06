<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetSubSubCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'asset_sub_category_id',
        'asset_category_id',
    ];

    public function assetSubCategory()
    {
        return $this->belongsTo(AssetSubCategory::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
