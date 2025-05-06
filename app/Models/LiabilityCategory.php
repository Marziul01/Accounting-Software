<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiabilityCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
    ];

    public function liabilitySubCategories()
    {
        return $this->hasMany(LiabilitySubCategory::class);
    }

    public function liabilities()
    {
        return $this->hasMany(Liability::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
