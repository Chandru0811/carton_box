<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'cart_id',
        'item_description',
        'quantity',
        'unit_price',
        'delivery_date',
        'coupon_code',
        'discount',
        'discount_percent',
        'seller_id',
        'product_id',
        'deal_type',
        'service_date',
        'service_time'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'seller_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
