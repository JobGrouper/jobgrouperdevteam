<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(CategoriesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(JobsTableSeeder::class);
        $this->call(EmployeeRequestsTableSeeder::class);
        $this->call(ExperienceTableSeeder::class);
        $this->call(EducationTableSeeder::class);
        $this->call(SalesTableSeeder::class);
        $this->call(PageTextsSeeder::class);

        Model::reguard();
    }
}
