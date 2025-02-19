<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{


    public function run(): void
    {
        $user = DB::table('users')->first();

        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $userId = $user->id;
        }

        // Insert a sample shop
        $shopId = DB::table('shops')->insertGetId([
            'owner_id' => $userId,
            'name' => 'Carton Box Guru',
            'legal_name' => 'Cloud ECS Infotech Pvt. Ltd.',
            'company_registeration_no' => 'CBG9698577',
            'slug' => 'carton_box_guru',
            'email' => 'info@ecsaio.com',
            'mobile' => '+65 8894 1306',
            'description' => 'Your one-stop shop for all carton and packing materials.',
            'external_url' => 'https://cartonboxguru.com',
            'street' => 'The Alexcier',
            'street2' => '237 Alexandra Road, #04-10, Singapore-159929.',
            'city' => 'Singapore',
            'zip_code' => '159929',
            'country' => 'Singapore',
            'state' => 'Alexandra',
            'current_billing_plan' => 'free',
            'account_holder' => 'Carton Box Guru',
            'account_type' => 'Savings',
            'account_number' => '9876543216789',
            'bank_name' => '',
            'bank_address' => '',
            'bank_code' => '',
            'payment_id' => '',
            'active' => true,
            'shop_ratings' => 4.5,
            'shop_type' => '',
            'logo' => 'http://127.0.0.1:8000/assets/images/cb_logo.png',
            'banner' => '',
            'map_url' => 'https://www.google.com/maps/place/The+Alexcier/@1.2916847,103.8111868,17z/data=!3m2!4b1!5s0x31da1a2cf1b2be13:0x7b0f9d88a36fdfbb!4m6!3m5!1s0x31da1bb95520771b:0xf2b9dfa378aa9a6e!8m2!3d1.2916793!4d103.8137617!16s%2Fg%2F11gyxjfkjk?entry=ttu&g_ep=EgoyMDI0MDkxOC4xIKXMDSoASAFQAw%3D%3D',
            'shop_lattitude' => 1.29168466305479,
            'shop_longtitude' => 103.81375097116381,
            'address' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert shop hours
        DB::table('shop_hours')->insert([
            'shop_id' => $shopId,
            'daily_timing' => json_encode([
                'Monday' => '9:00 AM - 7:00 PM',
                'Tuesday' => '9:00 AM - 7:00 PM',
                'Wednesday' => '9:00 AM - 7:00 PM',
                'Thursday' => '9:00 AM - 7:00 PM',
                'Friday' => '9:00 AM - 7:00 PM',
                'Saturday' => '10:00 AM - 7:00 PM',
                'Sunday' => '10:00 AM - 7:00 PM',
            ]),
        ]);

        // Insert shop policies
        DB::table('shop_policies')->insert([
            'shop_id' => $shopId,
            'refund_policy' => '7-day refund policy.',
            'cancellation_policy' => 'Cancellations must be made within 24 hours.',
            'shipping_policy' => 'Ships within 3-5 business days.',
        ]);
    }
}
