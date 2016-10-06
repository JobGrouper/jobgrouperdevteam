<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = array(
            ['id' => 1, 'title' => 'Web & Mobile Category'],
            ['id' => 2, 'title' => 'Design & Creative'],
            ['id' => 3, 'title' => 'Sales & Marketing'],
            ['id' => 4, 'title' => 'Customer Service'],
            ['id' => 5, 'title' => 'Writing & Translation'],
            ['id' => 6, 'title' => 'Legal'],
            ['id' => 7, 'title' => 'Admin Support'],
        );
        // Loop through each user above and create the record for them in the database
        foreach ($categories as $category)
        {
            Category::create($category);
        }
    }
}
