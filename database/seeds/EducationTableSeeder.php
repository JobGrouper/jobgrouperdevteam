<?php

use Illuminate\Database\Seeder;
use App\Education;

class EducationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $education = array(
            ['user_id'=> 4, 'title' => 'College of Marine and River Fleet', 'date_from' => '2016-04-01', 'date_to' => '2016-05-12', 'additional_info' => 'Junior'],
            ['user_id'=> 4, 'title' => 'NUFT', 'date_from' => '2016-06-01', 'date_to' => '2016-07-03', 'additional_info' => 'Bakalaur'],
        );
        // Loop through each user above and create the record for them in the database
        foreach ($education as $education)
        {
            Education::create($education);
        }
    }
}
