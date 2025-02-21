<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'countries';

    protected $fillable = [
        'country_name',
        'flag',
        'currency_symbol',
        'currency_code',
        'social_links',
        'address',
        'phone',
        'email',
        'color_code',
        'country_code'
    ];

    protected $dates = ['deleted_at'];
}
