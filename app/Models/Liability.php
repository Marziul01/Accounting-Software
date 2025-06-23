<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liability extends Model
{
    protected $fillable = [
        'name', 'slug', 'amount', 'description',
        'category_id', 'subcategory_id', 'subsubcategory_id',
        'national_id', 'mobile', 'email', 'father_name', 'father_mobile',
        'mother_name', 'mother_mobile', 'spouse_name', 'spouse_mobile',
        'present_address', 'permanent_address',
        'sms_enabled', 'email_enabled',
        'photo', 'user_name', 'entry_date',
        'send_sms', 'send_email', 'contact_id','status'
    ];
    

    public function category()
    {
        return $this->belongsTo(LiabilityCategory::class , 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(LiabilitySubCategory::class , 'subcategory_id');
    }

    public function subsubcategory()
    {
        return $this->belongsTo(LiabilitySubSubCategory::class , 'subsubcategory_id');
    }

    public function transactions()
    {
        return $this->hasMany(LiabilityTransaction::class, 'liability_id')->orderBy('transaction_date', 'asc');;
    }

    public function allTransactions()
    {
        return $this->hasMany(LiabilityTransaction::class, 'liability_id')->orderBy('transaction_date', 'asc');;
    }

    public function contact(){
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
