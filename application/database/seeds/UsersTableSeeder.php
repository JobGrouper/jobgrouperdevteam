<?php

use Illuminate\Database\Seeder;
use  App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $users = array(
            ['id'=> 1, 'first_name' => 'John', 'last_name' => 'Ling', 'user_type' => 'buyer', 'email' => 'testmail1@ukr.net', 'password' => bcrypt('qwerty'), 'email_confirmed' => true],
            ['id'=> 2, 'first_name' => 'Max', 'last_name' => 'Booth', 'user_type' => 'buyer', 'email' => 'testmail2@ukr.net', 'password' => bcrypt('qwerty'), 'email_confirmed' => true],
            ['id'=> 3, 'first_name' => 'Joey', 'last_name' => 'Case', 'user_type' => 'employee', 'email' => 'testmail3@ukr.net', 'password' => bcrypt('qwerty'), 'email_confirmed' => true],
            ['id'=> 4, 'first_name' => 'Pavel', 'last_name' => 'Stepanenko', 'user_type' => 'employee', 'email' => 'pavel.stepanenko94@gmail.com', 'password' => bcrypt('123'), 'email_confirmed' => true],
            ['id'=> 5, 'first_name' => 'Andrii', 'last_name' => 'Ovcharuk', 'user_type' => 'employee', 'email' => 'ovch2011@gmail.com', 'password' => bcrypt('rfvbrflpt'), 'email_confirmed' => true],
            ['id'=> 6, 'first_name' => 'Irunya', 'last_name' => 'Samary', 'user_type' => 'buyer', 'email' => 'test560@ukr.net', 'password' => bcrypt('qwerty'), 'email_confirmed' => true],
            ['id'=> 7, 'first_name' => 'Main', 'last_name' => 'Admin',  'user_type' => 'buyer', 'role' => 'admin', 'email' => 'admin@ukr.net', 'password' => bcrypt('qwerty'), 'email_confirmed' => true],
            ['id'=> 8, 'first_name' => 'Sam', 'last_name' => 'Samuel',  'user_type' => 'employee',  'email' => 'samarytest@gmail.com', 'password' => bcrypt('qwerty'), 'email_confirmed' => true],

        );
        // Loop through each user above and create the record for them in the database
        foreach ($users as $user)
        {
            User::create($user);
        }
    }
}
