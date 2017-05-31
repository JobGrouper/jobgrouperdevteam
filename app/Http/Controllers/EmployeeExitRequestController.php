<?php

namespace App\Http\Controllers;

use App\EmployeeExitRequest;
use App\User;
use Illuminate\Http\Request;

use App\Operations\EndAllEarlyBirdsOP;

use App\Http\Requests;
use Auth;
use App\Job;
use Mail;

class EmployeeExitRequestController extends Controller
{
    /**
     * Creating new  request from employee to exit from  the job card
     */
    public function store(Request $request, EndAllEarlyBirdsOP $end_all_early_birds){
        $responseData = array();

        $employee = Auth::user();
        //$employee = User::find(4);
        $job = Job::find($request->job_id);

        if(!isset($job->id)){
            $responseData['error'] = true;
            $responseData['status'] = 1;
            $responseData['info'] = 'Job not found';
            return response($responseData, 200);
        }

        //Check if user is employee
        if($employee->user_type != 'employee'){
            $responseData['error'] = true;
            $responseData['status'] = 2;
            $responseData['info'] = 'User is not employee';
            return response($responseData, 200);
        }

        //Check if user is employee of this job
        if($job->employee_id != $employee->id){
            $responseData['error'] = true;
            $responseData['status'] = 3;
            $responseData['info'] = 'User is not employee of this card';
            return response($responseData, 200);
        }

        //Check if user has not already make request to this card
        if (EmployeeExitRequest::where('employee_id', '=', $employee->id)->where('job_id', '=', $request->job_id)->count() > 0) {
            $responseData['error'] = true;
            $responseData['status'] = 4;
            $responseData['info'] = 'You already have done request to exit from this job';
            return response($responseData, 200);
        }



        $this->validate($request, [
            'job_id' => 'job_id'
        ]);

        switch($job->status) {
            case 'waiting':
                //If the job is not "working", remove the employee from the job immediately
                $job->employee_id = null;
                $job->save();
                $employee->employee_requests()->where('job_id', $job->id)->delete();

		// End all early bird buyers
		$end_all_early_birds->go($job);

                $responseData['error'] = false;
                $responseData['status'] = 1;
                $responseData['info'] = 'Job successfully deleted';
                break;
            case 'working':
                //Create exit request. Employee will leave the card at 2 weeks
                $EmployeeExitRequest = $employee->employee_exit_requests()->create([
                    'job_id' => $request->job_id,
                ]);
                if(!$EmployeeExitRequest->id){

                    $responseData['error'] = true;
                    $responseData['status'] = -1;
                    $responseData['info'] = 'Something went wrong. Please try again later.';
                    return response($responseData, 500);
                }

                //Notify buyers employee will leave work
                $orders = $job->sales()->get();
                foreach ($orders as $order){
                    $buyer = $order->buyer()->first();

                    Mail::send('emails.employee_leaves_job',['job_name'=>$job->title, 'employee_name'=>$employee->full_name],function($u) use ($buyer)
                    {
                        $u->from('admin@jobgrouper.com');
                        $u->to($buyer->email);
                        $u->subject('Employee will leave the work');
                    });
                }

                $responseData['error'] = false;
                $responseData['status'] = 0;
                $responseData['info'] = 'Request successfully created';
                break;
        }

        return response($responseData, 200);

    }
}
