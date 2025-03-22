<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('countries')->insert([
            [
                'country_name' => 'Singapore',
                'flag' => 'assets/images/Flag_of_Singapore.webp',
                'currency_symbol' => 'S$',
                'currency_code' => 'SGD',
                'social_links' => json_encode([
                    [
                        's_icon' => 'fa-solid fa-user',
                        's_link' => 'https://instagram.com/sg'
                    ]
                ]),
                'address' => 'The Alexcier, 237 Alexandra Road, #04-10, Singapore-159929.',
                'phone' => '8894 1306',
                'email' => 'contact@singapore.com',
                'color_code' => '#FF0000',
                'phone_number_code' => '65',
                'country_code' => 'sg',
                'default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_name' => 'India',
                'flag' => 'assets/images/Flag_of_India.png',
                'currency_symbol' => '₹',
                'currency_code' => 'INR',
                'social_links' => json_encode([
                    [
                        's_icon' => 'fa-solid fa-user',
                        's_link' => 'https://instagram.com/in'
                    ]
                ]),
                'address' => '456 India Rd, New Delhi, India',
                'phone' => '+91 98765 43210',
                'email' => 'contact@india.com',
                'color_code' => '#FF9933',
                'phone_number_code' => '91',
                'country_code' => 'in',
                'default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
