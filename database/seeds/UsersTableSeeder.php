<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('users')->truncate();
        DB::table('users')->insert(
            [
                'first_name' => 'Aref',
                'last_name' => 'Razavi',
                'email' => 'arefrazavi@gmail.com',
                'password' => 'kiana_1989',
            ]
        );
    }
}
