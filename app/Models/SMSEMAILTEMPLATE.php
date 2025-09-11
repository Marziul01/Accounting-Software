<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SMSEMAILTEMPLATE extends Model
{
    public function contacts(){
        return $this->hasMany(OcassionContact::class, 'ocassion_id');
    }
}
