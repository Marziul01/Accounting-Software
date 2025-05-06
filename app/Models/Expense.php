<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'amount',
        'description',
        'status',
        'slug',
        'expense_sub_category_id',
        'expense_category_id',
        'name',
        'date',

    ];

    public function expenseSubCategory()
    {
        return $this->belongsTo(ExpenseSubCategory::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
