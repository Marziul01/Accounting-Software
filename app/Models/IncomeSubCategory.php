<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeSubCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'income_category_id',
    ];

    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class);
    }
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}
