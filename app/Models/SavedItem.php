<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_number', 'user_id', 'deal_id', 'ip_address', 'cart_number'];

    public function deal()
    {
        return $this->belongsTo(Product::class, 'deal_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
