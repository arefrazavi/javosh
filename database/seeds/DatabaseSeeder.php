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
        $this->call(AspectCategorySeeder::class);
        $this->call(AspectsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(StopWordsSeeder::class);
        $this->call(TypesTableSeeder::class);
        $this->call(SentinelDatabaseSeeder::class);
        $this->call(SummarizationMethodsTableSeeder::class);
    }
}
