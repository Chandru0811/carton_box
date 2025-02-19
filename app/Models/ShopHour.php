<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopHour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'daily_timing'
    ];

    protected $casts = [
        'daily_timing' => 'array',
    ];

    protected $dates = ['deleted_at'];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
