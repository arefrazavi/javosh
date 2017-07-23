<?php

use Illuminate\Database\Seeder;

class AspectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('aspects')->truncate();
        DB::table('aspects')->insert([
            [
                'title' => 'ارزش خرید به نسبت قیمت',
                'type' => 0,
                'category_id' => 4
                ,

            ],
            [
                'title' => 'کیفیت ساخت',
                'type' => 0,
                'category_id' => 4

            ],
            [
                'title' => 'امکانات و قابلیت ها',
                'type' => 0,
                'category_id' => 4

            ],
            [
                'title' => 'سهولت استفاده',
                'type' => 0,
                'category_id' => 4

            ],
            [
                'title' => 'طراحی و ظاهر',
                'type' => 0,
                'category_id' => 4

            ],
            [
                'title' => 'نوآوری',
                'type' => 0,
                'category_id' => 4

            ],
        ]);
    }
}
