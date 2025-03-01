<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            // Categories for Electronics
            [
                'category_group_id' => 1,
                'name' => 'All Sizes New Carton Box',
                'slug' => 'all_sizes_new_carton_box',
                'description' => 'New carton boxes in various sizes for packing and moving',
                'active' => 1,
            ],
            [
                'category_group_id' => 1,
                'name' => 'House Moving Carton Box',
                'slug' => 'house_moving_carton_box',
                'description' => 'Carton boxes suitable for house moving and storage',
                'active' => 1,
            ],
            [
                'category_group_id' => 1,
                'name' => 'Postal / Shipping Carton Box',
                'slug' => 'postal_shipping_carton_box',
                'description' => 'Carton boxes designed for postal and shipping purposes',
                'active' => 1,
            ],
            [
                'category_group_id' => 1,
                'name' => 'E-Commerce Carton Box',
                'slug' => 'e_commerce_carton_box',
                'description' => 'Carton boxes for packaging and shipping e-commerce orders',
                'active' => 1,
            ],
            [
                'category_group_id' => 1,
                'name' => 'Cake Boxes',
                'slug' => 'cake_boxes',
                'description' => 'Boxes for cakes, pastries, and baked goods',
                'active' => 1,
            ],
            [
                'category_group_id' => 1,
                'name' => 'Gift Boxes',
                'slug' => 'gift_boxes',
                'description' => 'Boxes for gifts, presents, and special occasions',
                'active' => 1,
            ],
            // used carton boxes
            [
                'category_group_id' => 2,
                'name' => 'All Sizes Used Carton Box',
                'slug' => 'all_sizes_used_carton_box',
                'description' => 'Used carton boxes in various sizes for packing and moving',
                'active' => 1,
            ],
            [
                'category_group_id' => 2,
                'name' => 'Assorted Box',
                'slug' => 'assorted_box',
                'description' => 'Mixed carton boxes in different sizes and shapes',
                'active' => 1,
            ],
            [
                'category_group_id' => 2,
                'name' => 'TV Carton Box',
                'slug' => 'tv_carton_box',
                'description' => 'Carton boxes designed for packing and moving televisions',
                'active' => 1,
            ],
            [
                'category_group_id' => 2,
                'name' => 'Buy Back Box',
                'slug' => 'buy_back_box',
                'description' => 'Carton boxes that can be returned for a refund or credit',
                'active' => 1,
            ],
            // packing materials
            [
                'category_group_id' => 3,
                'name' => 'Bubble Wrap',
                'slug' => 'bubble_wrap',
                'description' => 'Protective packaging material with air-filled bubbles for cushioning',
                'active' => 1,
            ],
            [
                'category_group_id' => 3,
                'name' => 'Tape',
                'slug' => 'tape',
                'description' => 'Adhesive tapes for sealing and securing carton boxes',
                'active' => 1,
            ],
            [
                'category_group_id' => 3,
                'name' => 'All Packaging Products',
                'slug' => 'all_packaging_products',
                'description' => 'Various products for packaging, including tape, bubble wrap, and labels',
                'active' => 1,
            ],
            [
                'category_group_id' => 3,
                'name' => 'Stationeries',
                'slug' => 'stationeries',
                'description' => 'Office supplies and stationery items for packing and labeling',
                'active' => 1,
            ],
            [
                'category_group_id' => 3,
                'name' => 'Home Essentials',
                'slug' => 'home_essentials',
                'description' => 'Essential items for home and office use, including tape, scissors, and markers',
                'active' => 1,
            ],
            [
                'category_group_id' => 3,
                'name' => 'Gift Packaging',
                'slug' => 'gift_packaging',
                'description' => 'Packaging materials for wrapping and presenting gifts',
                'active' => 1,
            ],
        ]);
    }
}
