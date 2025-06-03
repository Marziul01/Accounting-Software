<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentTransaction extends Model
{
    protected $fillable = [
        'investment_id',
        'amount',
        'transaction_type',
        'transaction_date',
        'description',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
    
}
