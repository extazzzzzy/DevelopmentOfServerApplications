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
            ['username' => 'Danil_Admin', 'email' => 'daniladmin@mail.ru', 'password' => 'Aa123@@@', 'birthday' => '2004-03-12'],
            ['username' => 'Alex_User', 'email' => 'alexuser@mail.ru', 'password' => 'Aa123@@@', 'birthday' => '2004-04-27'],
        ]);
    }
}
