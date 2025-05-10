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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
