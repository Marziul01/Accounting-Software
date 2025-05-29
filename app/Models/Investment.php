<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $fillable = [
        'name',
        'description',
        'amount',
        'investment_sub_category_id',
        'slug',
        'date',
        'investment_category_id',
        'investment_type',
    ];

    public function investmentSubCategory()
    {
        return $this->belongsTo(InvestmentSubCategory::class);
    }
    public function investmentCategory()
    {
        return $this->belongsTo(InvestmentCategory::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function transactions()
    {
        return $this->hasMany(InvestmentTransaction::class);
    }
}
