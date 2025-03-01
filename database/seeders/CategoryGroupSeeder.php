<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('category_groups')->insert([
            [
                'name' => 'New Carton Box',
                'slug' => 'new_carton_box',
                'description' => 'Group of new carton box categories',
                'active' => 1,
                'order' => 1,
            ],
            [
                'name' => 'User Carton Box',
                'slug' => 'user_carton_box',
                'description' => 'Group of user carton box categories',
                'active' => 1,
                'order' => 2,
            ],
            [
                'name' => 'Packing Materials',
                'slug' => 'packing_materials',
                'description' => 'Group of packing materials categories',
                'active' => 1,
                'order' => 3,
            ],
        ]);
    }
}
