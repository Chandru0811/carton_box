<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon', 'image_path', 'active', 'order', 'country_id'];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
