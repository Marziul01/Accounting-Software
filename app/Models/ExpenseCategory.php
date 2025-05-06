<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
    ];

    public function expenseSubCategories()
    {
        return $this->hasMany(ExpenseSubCategory::class);
    }
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
