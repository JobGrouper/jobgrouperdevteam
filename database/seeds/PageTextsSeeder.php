<?php

use Illuminate\Database\Seeder;
use App\PageText;

class PageTextsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pageTexts = array(
            ['value' => "Hire Industry-Leading Talent on a <span>Startup Budget</span>"],
            ['value' => "Share the cost with other startups using our unique groupbuy employment method"],
            ['value' => "This is dummy copy. It is not meant to be read. It has been placed here solely to demonstrate the look and feel of finished, typeset text. Only for show. He who searches for meaning here will be sorely disappointed."],
            ['value' => "This is dummy copy. It is not meant to be read. It has been placed here solely to demonstrate the look and feel of finished, typeset text. Only for show. He who searches for meaning here will be sorely disappointed."],
            ['value' => "Hire the World's Top People for your Startup, at a Fraction of the Cost"],
            ['value' => "Share the cost with other startups using our unique groupbuy employment method"],
            ['value' => "HOW IT WORKS"],
            ['value' => "Jobgrouper.com"],
            ['value' => "All Rights Reserved"],
            ['value' => "Copyright © 2016"],
            ['value' => "ADDITIONAL INFORMATION"],
            ['value' => "RECENT ACTIVITY"],
            ['value' => "At Jobgrouper, we use groupbuys to guarantee you a stable, full-time job from tasks that might ordinarily be a series of part-time contracts. <br> Once the required number of buyers have been reached, buyers will be billed monthly, guaranteeing you a yearly salary of the following amount:"],
        );
        // Loop through each user above and create the record for them in the database
        foreach ($pageTexts as $pageText)
        {
            PageText::create($pageText);
        }
    }
}
