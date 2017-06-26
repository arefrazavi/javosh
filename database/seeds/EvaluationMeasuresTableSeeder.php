<?php

use Illuminate\Database\Seeder;

class EvaluationMeasuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('evaluation_results')->truncate();
        DB::table('evaluation_measures')->insert([
            [
                'title' => 'Precision',
                'alias' => 'دقت',
            ],
            [
                'title' => 'Recall',
                'alias' => 'فراخوانی'
            ],
            [
                'title' => 'F-Measure',
                'alias' => ''
            ],
        ]);
    }
}
