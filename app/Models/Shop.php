<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'legal_name',
        'company_registeration_no',
        'slug',
        'email',
        'mobile',
        'description',
        'external_url',
        'street',
        'street2',
        'city',
        'zip_code',
        'country',
        'state',
        'current_billing_plan',
        'account_holder',
        'account_type',
        'account_number',
        'bank_name',
        'bank_address',
        'bank_code',
        'payment_id',
        'trial_ends_at',
        'active',
        'shop_ratings',
        'shop_type',
        'logo',
        'banner',
        'map_url',
        'shop_lattitude',
        'shop_longtitude',
        'address'
    ];

    protected $dates = ['deleted_at'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function hour()
    {
        return $this->hasOne(ShopHour::class, 'shop_id');
    }

    public function policy()
    {
        return $this->hasOne(ShopPolicy::class, 'shop_id');
    }
}
