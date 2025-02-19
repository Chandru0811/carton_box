<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'resize_path', 'order', 'imageable_id', 'imageable_type', 'type'];

    public function imageable()
    {
        return $this->morphTo();
    }
}
