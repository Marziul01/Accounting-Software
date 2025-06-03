<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetTransaction extends Model
{
    protected $fillable = [
        'asset_id', 'amount', 'transaction_type', 'transaction_date' , 'description'
    ];
}
