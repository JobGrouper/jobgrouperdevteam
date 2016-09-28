<?php

use Illuminate\Database\Seeder;
use App\Experience;

class ExperienceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $experience = array(
            ['user_id'=> 4, 'title' => 'Web Designer', 'date_from' => '2016-04-01', 'date_to' => '2016-05-12', 'additional_info' => 'bla bla bla'],
            ['user_id'=> 4, 'title' => 'BackEnd Developer', 'date_from' => '2016-06-01', 'date_to' => '2016-07-03', 'additional_info' => 'bla bla bla'],
        );
        // Loop through each user above and create the record for them in the database
        foreach ($experience as $experience)
        {
            Experience::create($experience);
        }
    }
}
