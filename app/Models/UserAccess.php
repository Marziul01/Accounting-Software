<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    protected $fillable = [
        'user_id',
        'admin_panel',
        'sms_and_email',
        'contact',
        'income',
        'expense',
        'investment',
        'asset',
        'liability',
        'bankbook',
        'accounts',
        'asset_category_table', 'asset_name_table', 'asset_category', 'asset_subcategory' , 'liability_category_table', 'liability_name_table', 'liability_category', 'liability_subcategory' , 'income_category' , 'expense_category', 'investments_category'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
