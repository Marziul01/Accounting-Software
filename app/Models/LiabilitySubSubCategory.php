<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiabilitySubSubCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'liability_sub_category_id',
        'liability_category_id',
    ];

    public function liabilitySubCategory()
    {
        return $this->belongsTo(LiabilitySubCategory::class);
    }
    public function liabilityCategory()
    {
        return $this->belongsTo(LiabilityCategory::class);
    }

    public function liabilities()
    {
        return $this->hasMany(Liability::class);
    }



    public function getRouteKeyName()
    {
        return 'slug';
    }
}
