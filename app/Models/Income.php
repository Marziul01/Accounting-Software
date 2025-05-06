<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'income_category_id',
        'income_sub_category_id',
        'date',
        'amount',
    ];

    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function incomeSubCategory()
    {
        return $this->belongsTo(IncomeSubCategory::class);
    }
    
}
