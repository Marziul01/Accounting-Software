<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentIncome extends Model
{
    protected $fillable = [
        'investment_id',
        'category_id',
        'subcategory_id',
        'amount',
        'date',
        'description',
        'income_id',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(IncomeSubCategory::class, 'subcategory_id');
    }
    public function income()
    {
        return $this->belongsTo(Income::class, 'income_id');
    }
    
}
