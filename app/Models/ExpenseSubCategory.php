<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseSubCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'expense_category_id',
    ];

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
