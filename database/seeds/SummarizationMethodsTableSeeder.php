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
                'title' => 'E_TFIDF_SCB',
                'alias' => 'خلاصه مبتنی بر مرکز با احساس (رده بندی مبتنی بر بی نظمی و خوشه بندی مبتنی بر TF-IDF)'
            ],
            [
                'title' => 'E_WE_SCB',
                'alias' => 'خلاصه مبتنی بر مرکز با احساس (رده بندی مبتنی بر بی نظمی و خوشه بندی مبتنی بر تعبیه کلمه)'
            ],
            [
                'title' => 'AF_TFIDF_SCB',
                'alias' => 'خلاصه مبتنی بر مرکز با احساس (رده بندی مبتنی بر فرکانس جنبه و خوشه بندی مبتنی بر TF-IDF)'
            ],
            [
                'title' => 'AF_WE_SCB',
                'alias' => 'خلاصه مبتنی بر مرکز با احساس (رده بندی مبتنی بر فرکانس جنبه و خوشه بندی مبتنی بر تعبیه کلمه)'
            ],

        ]);
    }
}
