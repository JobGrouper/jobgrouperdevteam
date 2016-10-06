<?php

use Illuminate\Database\Seeder;
use App\Job;

class JobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobs = array(
            ['category_id' => 1, 'title' => 'Creating logos', 'description' => 'Lorem ipsum dolor sit amet', 'salary' => '5', 'max_clients_count' => '10', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'Improve the existing landing page', 'description' => 'You have to show the result in 4 days Need to show two variants We will choose the best one', 'salary' => '600', 'max_clients_count' => '3', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'Work with video', 'description' => 'Responsibilities: find an interesting and funny videos convert format (if it is nedded) upload into site moderate feedback', 'salary' => '500', 'max_clients_count' => '6', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'Moderator of social group in Internet', 'description' => 'Girls and boys. If you love to chat and to meet with new people we propose you to be a moderator. More detail in privat messages', 'salary' => '500', 'max_clients_count' => '4', 'hot' => '0'],
            ['category_id' => 3, 'title' => 'To sell IOS app', 'description' => 'App which can be usful for pet\'s fan. Shops, vets, groomers etc.', 'salary' => '1000', 'max_clients_count' => '1', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'Work with database', 'description' => 'Permanently. Working experience with DB at least 3 years', 'salary' => '550', 'max_clients_count' => '3', 'hot' => '0'],
            ['category_id' => 5, 'title' => 'Translate web application into different languages', 'description' => 'We need translators from English to poland, franch, germany, norwey etc.', 'salary' => '850', 'max_clients_count' => '20', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'Strategy of brand-building', 'description' => 'Our brand is too young. We want to get to market as soon as it is possible. So, we are seeking a super marketologist who will help us', 'salary' => '1200', 'max_clients_count' => '3', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'Design of all pages. Web app about Romania', 'description' => 'To design all pages of app. It will be informational app about living in Romania', 'salary' => '750', 'max_clients_count' => '7', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'To create a log in form', 'description' => 'Create a form for private and wholesale customers. Login must be separate', 'salary' => '200', 'max_clients_count' => '2', 'hot' => '0'],
            ['category_id' => 13, 'title' => 'To compose \"Terms & Conditions\"', 'description' => 'Loyer, who practice in private low', 'salary' => '400', 'max_clients_count' => '2', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'To develope mobile app for accountants', 'description' => 'The main goal of app is to make accountant\'s life easier. ', 'salary' => '5000', 'max_clients_count' => '6', 'hot' => '0'],
            ['category_id' => 1, 'title' => 'Exclusive content', 'description' => 'Authors must write a tex which will sell our goods. Uxlusive more than 95%', 'salary' => '320', 'max_clients_count' => '9', 'hot' => '0'],
        );
        // Loop through each user above and create the record for them in the database
        foreach ($jobs as $job)
        {
            Job::create($job);
        }
    }
}
