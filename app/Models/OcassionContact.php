<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OcassionContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'ocassion_id', 
        'contact_id', 
        'sented', 
        'next_send'
    ];

    public function contact(){
        return $this->belongsTo(Contact::class,'contact_id');
    }
}
