<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $fillable = [
        'shop_id',
        'name',
        'deal_type',
        'category_id',
        'brand',
        'description',
        'slug',
        'original_price',
        'discounted_price',
        'discount_percentage',
        'start_date',
        'end_date',
        'stock',
        'sku',
        'active',
        'coupon_code',
        'specifications',
        'varient',
        'delivery_days'
    ];

    protected $dates = ['deleted_at', 'start_date', 'end_date'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    // public function views()
    // {
    //     return $this->hasMany(DealViews::class, 'deal_id');
    // }

    // public function clicks()
    // {
    //     return $this->hasMany(DealClick::class, 'deal_id');
    // }

    // public function bookmark()
    // {
    //     return $this->hasMany(Bookmark::class, 'deal_id');
    // }

    // public function review()
    // {
    //     return $this->hasMany(Review::class, 'product_id');
    // }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productMedia()
    {
        return $this->morphMany(ProductMedia::class, 'imageable');
    }
}
