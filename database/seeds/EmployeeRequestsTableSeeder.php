<?php

use Illuminate\Database\Seeder;
use App\EmployeeRequest;

class EmployeeRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employeeRequests = array(
            ['employee_id'=> 3, 'job_id' => 1, 'status' => 'approved'],
        );
        // Loop through each user above and create the record for them in the database
        foreach ($employeeRequests as $employeeRequest)
        {
            EmployeeRequest::create($employeeRequest);
        }
    }
}
