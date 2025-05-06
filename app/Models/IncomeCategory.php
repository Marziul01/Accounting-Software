<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    protected $fillable = [
        'name',
        'desc',
        'status',
        'slug',
    ];

    public function incomeSubCategories()
    {
        return $this->hasMany(IncomeSubCategory::class);
    }
}
