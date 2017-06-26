<?php

use Illuminate\Database\Seeder;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('types')->truncate();
        DB::table('types')->insert([
            ['type' => 'products'],
            ['type' => 'comment'],
            ['type' => 'vote'],
            ['type' => 'score'],
            ['type' => 'like'],
            ['type' => 'dislike'],
            ['type' => 'aspects']
        ]);
    }
}
