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
        'national_id',
        'father_name',
        'father_mobile',
        'mother_name',
        'mother_mobile',
        'spouse_name',
        'spouse_mobile',
        'present_address',
        'permanent_address',
        'send_email',
    ];
}
