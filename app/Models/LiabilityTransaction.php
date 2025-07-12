<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiabilityTransaction extends Model
{
    protected $fillable = [
        'liability_id', 'amount', 'transaction_type', 'transaction_date','description'
    ];

    public function liability(){
        return $this->belongsTo(Liability::class,'liability_id');
    }
}
