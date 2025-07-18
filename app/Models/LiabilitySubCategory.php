<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiabilitySubCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'liability_category_id',
    ];

    public function liabilityCategory()
    {
        return $this->belongsTo(LiabilityCategory::class);
    }

    public function liabilitySubSubCategories()
    {
        return $this->hasMany(LiabilitySubSubCategory::class);
    }

    public function liabilities()
    {
        return $this->hasMany(Liability::class, 'subcategory_id');
    }




    public function getRouteKeyName()
    {
        return 'slug';
    }
}
