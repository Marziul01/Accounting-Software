<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSchedule extends Model
{
    protected $fillable = [
        'from',
        'to',
        'amount',
        'start_date',
        'end_date',
        'description',
        'status',
        'infinite',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'from', 'id');
    }

    public function toBankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'to', 'id');
    }
}
