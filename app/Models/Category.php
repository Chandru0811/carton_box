<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_group_id', 'name', 'slug', 'description', 'icon', 'active', 'country_id',];

    protected $dates = ['deleted_at'];

    public function categoryGroup()
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
