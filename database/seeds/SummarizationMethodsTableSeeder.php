<?php

use Illuminate\Database\Seeder;

class SummarizationMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('summarization_methods')->truncate();
        DB::table('summarization_methods')->insert([
            [
                'title' => 'GS',
                'alias' => 'خلاصه طلایی',
            ],
            [
                'title' => 'Random',
                'alias' => 'خلاصه تصادفی'
            ],
            [
                'title' => 'CB',
                'alias' => 'خلاصه مبتنی بر مرکز'
            ],
            [
                'title' => 'SCB',
                'alias' => 'خلاصه مبتنی بر مرکز با احساس'
            ],
            [
                'title' => 'SWECB',
                'alias' => 'خلاصه مبتنی بر مرکز و تعبیه کلمه با احساس'
            ],

        ]);
    }
}
