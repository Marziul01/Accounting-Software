<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'slug',
        'address',
        'mobile_number',
        'date_of_birth',
        'marriage_date',
        'sms_option',
        'image',
    ];
}
