<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
    ];

    public function investmentSubCategories()
    {
        return $this->hasMany(InvestmentSubCategory::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
