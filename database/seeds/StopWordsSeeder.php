<?php

use Illuminate\Database\Seeder;

class StopWordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stopWordsFile = array_map('str_getcsv', file(base_path('data/words/stop_words.csv')));

        $stopWords = [];
        foreach ($stopWordsFile as $row) {
            if ($row[0]) {
                $stopWord = trim($row[0]);
            }
            $stopWords[$stopWord] = ['value' => $stopWord];

            if ($row[1]) {
                $stopWord = trim($row[1]);
            }
            $stopWords[$stopWord] = ['value' => $stopWord];
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('stop_words')->truncate();
        DB::table('stop_words')->insert($stopWords);
    }
}
