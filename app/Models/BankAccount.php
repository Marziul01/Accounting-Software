<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'account_holder_name',
        'account_number',
        'bank_name',
        'account_type',
        'branch_name',
        'nominee_name',
    ];

    // Define any relationships or additional methods here
    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function getBalanceAttribute()
    {
        $credit = $this->transactions()->where('transaction_type', 'credit')->sum('amount');
        $debit = $this->transactions()->where('transaction_type', 'debit')->sum('amount');
        return $credit - $debit;
    }

}
