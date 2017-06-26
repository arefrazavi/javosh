<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('categories')->truncate();
        DB::table('categories')->insert([
            [
                'title' => 'Main',
                'alias' => 'فروشگاه اینترنتی دیجی کالا',
                'parent_id' => 0
            ],
            [
                'title' => 'Electronic-Devices',
                'alias' => 'کالای دیجیتال',
                'parent_id' => 1
            ],
            [
                'title' => 'Mobile',
                'alias' => 'موبایل',
                'parent_id' => 2
            ],
            [
                'title' => 'Mobile-Phone',
                'alias' => 'گوشی موبایل',
                'parent_id' => 3
            ],
            [
                'title' => 'Laptop',
                'alias' => 'لپ‌تاپ',
                'parent_id' => 2
            ],
            [
                'title' => 'Laptop-Ultrabook',
                'alias' => 'لپ‌تاپ و الترابوک',
                'parent_id' => 5
            ],
            [
                'title' => 'Camera',
                'alias' => 'دوربین',
                'parent_id' => 1
            ],
            [
                'title' => 'Digital-Camera',
                'alias' => 'دوربین دیجیتال',
                'parent_id' => 7
            ]
        ]);
    }
}
