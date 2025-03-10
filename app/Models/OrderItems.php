<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_number',
        'product_id',
        'seller_id',
        'item_description',
        'quantity',
        'unit_price',
        'delivery_date',
        'coupon_code',
        'discount',
        'discount_percent',
        'deal_type',
        'service_date',
        'service_time',
        'shipping',
        'packaging',
        'handling',
        'taxes',
        'shipping_weight',
        'viewed_by_admin',
        'viewed_by_vendor'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
