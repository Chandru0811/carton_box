<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'link',
        'link_label',
        'bg_color',
        'image_path',
        'order',
        'country_id',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
