<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentSubCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'investment_category_id',
    ];

    public function investmentCategory()
    {
        return $this->belongsTo(InvestmentCategory::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
}
