<?php
use App\Job;
/**
 * Created by PhpStorm.
 * User: Админ
 * Date: 05.07.2016
 * Time: 11:47
 */
class JobTest extends TestCase
{

    public function testJobCanBeCreated()
    {
        $job = Job::create(['category_id'=> 1, 'employee_id' => 3, 'title' => 'Test job', 'description' => 'Test descr', 'salary' => 100, 'max_clients_count' => 10]);

        $latestJob = Job::latest()->first();

        $this->assertEquals($job->id, $latestJob->id);
        $this->assertEquals('Test job', $latestJob->title);
        $this->assertEquals('Test descr', $latestJob->description);

        $this->seeInDatabase('jobs', ['category_id'=> 1, 'employee_id' => 3, 'title' => 'Test job', 'description' => 'Test descr', 'salary' => 100, 'max_clients_count' => 10]);

        $job->delete();

    }
}