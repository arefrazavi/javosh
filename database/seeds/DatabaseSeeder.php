<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AspectsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(TypesTableSeeder::class);
        $this->call(EvaluationMeasuresTableSeeder::class);
        $this->call(SentinelDatabaseSeeder::class);
        $this->call(StopWordsSeeder::class);
        $this->call(SummarizationMethodsTableSeeder::class);
        $this->call(TypesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
