<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'refund_policy',
        'cancellation_policy',
        'shipping_policy'
    ];

    protected $dates = ['deleted_at'];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
