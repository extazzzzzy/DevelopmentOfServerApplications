<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            ['username' => 'DanilAdmin', 'email' => 'danyakovalugrasu@gmail.com', 'password' => bcrypt('Aa123@@@'), 'birthday' => '2004-03-12'],
            ['username' => 'AlexUser', 'email' => 'alexuser@mail.ru', 'password' => bcrypt('Aa123@@@'), 'birthday' => '2004-04-27'],
        ]);
    }
}
