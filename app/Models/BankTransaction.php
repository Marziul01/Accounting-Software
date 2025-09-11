<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'amount',
        'description',
        'bank_account_id',
        'transaction_type',
        'name',
        'transaction_id',
        'slug',
        'from_id',
        'from',
        'transfer_from',
        'transfer_to'
    ];

    // Define any relationships or additional methods here
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class , 'bank_account_id');
    }
}
