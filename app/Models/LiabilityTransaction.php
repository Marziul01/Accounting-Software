<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiabilityTransaction extends Model
{
    protected $fillable = [
        'liability_id', 'amount', 'transaction_type', 'transaction_date','description'
    ];
}
