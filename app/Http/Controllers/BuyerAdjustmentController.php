<?php

namespace App\Http\Controllers;

use App\BuyerAdjustment;
use App\BuyerAdjustmentRequest;
use App\Job;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use Validator;
use Mail;
use Carbon\Carbon;

class BuyerAdjustmentController extends Controller
{
    public function create(Request $request){

	    // TODO: Change the control structure of this function,
	    //  there are some common operations
	    //
        $v = Validator::make($request->all(),[
                'request_id' => 'required_without:job_id|numeric',
                'job_id' => 'required_without:request_id|numeric',
                'new_client_max' => 'required|numeric',
                'new_client_min' => 'required|numeric',
            ]
        );

        if($v->fails()){
            return ['status' => 'error', 'data' => $v->errors(), 'message' => 'validator_error'];
        }
        
        if($request->request_id){
            $buyerAdjustmentRequest = BuyerAdjustmentRequest::findOrFail($request->request_id);
            $job = $buyerAdjustmentRequest->job()->get()->first();
	    $employee = $buyerAdjustmentRequest->employee()->first();

	    $buyerAdjustmentRequest->status = 'accepted';
	    $buyerAdjustmentRequest->decision_date = Carbon::now();
	    $buyerAdjustmentRequest->save();

            $buyerAdjustment = BuyerAdjustment::create([
                'from_request_id' => $request->request_id,
                'job_id' => $job->id,
                'old_client_min' => $job->min_clients_count,
                'old_client_max' => $job->max_clients_count,
                'new_client_min' => $request->new_client_min,
                'new_client_max' => $request->new_client_max,
            ]);

	    $changes = $this->getChangesArray($job->min_clients_count, $job->max_clients_count,
		$request->new_client_min, $request->new_client_max);

            $job->min_clients_count = $request->new_client_min;
            $job->max_clients_count = $request->new_client_max;
            $job->save();

            //Mail for employee
            Mail::send('emails.buyer_adjustment_request_approved_to_employee', ['job_title'=>$job->title],function($u) use ($employee)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($employee->email);
                $u->subject('Your request has gone through');
            });

            if($buyerAdjustment->old_client_min >= $job->min_clients_count){
                // Starts plan
            }
            return ['status' => 'success'];
        }
        elseif($request->job_id){
            $job = Job::findOrFail($request->job_id);
            
            $buyerAdjustment = BuyerAdjustment::create([
                'job_id' => $job->id,
                'old_client_min' => $job->min_clients_count,
                'old_client_max' => $job->max_clients_count,
                'new_client_min' => $request->new_client_min,
                'new_client_max' => $request->new_client_max,
            ]);

	    $changes = $this->getChangesArray($job->min_clients_count, $job->max_clients_count,
		$request->new_client_min, $request->new_client_max);

            $job->min_clients_count = $request->new_client_min;
            $job->max_clients_count = $request->new_client_max;
            $job->save();

            if($buyerAdjustment->old_client_min >= $job->min_clients_count){
                // Starts plan
            }

            return json_encode(['status' => 'success']);

	    /*
	    $admins = User::where('role', 'admin')->get();
	    foreach ($admins as $admin){
		    //Mail to admin
		    Mail::send('emails.buyer_adjustment_request_approved_to_admin', ['job_title'=>$job->title],function($u) use ($employee)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to($employee->email);
			$u->subject('Your request has gone through');
		    });
	    }
	     */

	    /*
	    $buyers = $job->buyers()->get();
	    foreach ($buyers as $buyer) {

		    //Mail to buyers 
		    Mail::send('emails.buyer_adjustment_request_approved_to_buyers', ['job_title'=>$job->title, 'employee_name' => $employee->full_name, 'employee_first_name' => $employee->first_name, 'changes' => $changes],function($u) use ($employee, $job)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to($employee->email);
			$u->subject('The number of buyers for ' . $job->title . ' has been modified');
		    });
	    }
	     */
        }


    }

    public function create_request(Request $request){

        $v = Validator::make($request->all(),[
                'job_id' => 'required|numeric',
                'current_client_max' => 'required|numeric',
                'current_client_min' => 'required|numeric',
                'new_client_min' => 'required|numeric',
                'new_client_max' => 'required|numeric',

            ]
        );
        if ($v->fails())
        {
	    return response([
		'status' => 'X',
		'data' => $v->errors(),
		'message' => 'Failed, check error object',
	    ], 200);
        }

        $employee = Auth::user();
        $job = Job::findOrFail($request->job_id);
        
        $employee->buyerAdjustmentRequests()->create([
            'job_id' => $request->job_id,
            'employee_id' => $request->employee_id,
            'current_client_max' => $request->current_client_max,
            'current_client_min' => $request->current_client_min,
            'requested_client_max' => $request->new_client_max,
            'requested_client_min' => $request->new_client_min
        ]);

	$changes = $this->getChangesArray($request->current_client_min, $request->current_client_max,
		$request->new_client_min, $request->new_client_max);

        //Mail for admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin){
            Mail::send('emails.buyer_adjustment_request_to_admin', ['job_title'=>$job->title, 'employee_name' =>$employee->full_name, 'changes' => $changes],function($u) use ($admin)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($admin->email);
                $u->subject('Request to modify buyers');
            });
        }

        //Mail for employee
        Mail::send('emails.buyer_adjustment_request_to_employee', ['job_title'=>$job->title],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('Your request has gone through');
        });

        //Mail for buyers
        /*$buyers = $job->buyers()->get();
        foreach ($buyers as $buyer){
            Mail::send('emails.buyer_adjustment_request_to_buyers', ['job_title'=>$job->title, 'employee_name' => $employee->full_name, 'changes' => $changes],function($u) use ($buyer, $job)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($buyer->email);
                $u->subject($job->title . ' may be modified soon');
            });
        }*/


	    return response([
		'status' => 'OK',
		'data' => null,
		'message' => 'Buyer Request sent',
	    ], 200);
    }

    public function deny_request($requestID){
        $buyerAdjustmentRequest = BuyerAdjustmentRequest::findOrFail($requestID);
        if($buyerAdjustmentRequest->status != 'pending'){
            return response([
                'status' => 'error',
                'data' => null,
                'message' => 'request_already_processed',
            ], 200);
        }

        $employee = $buyerAdjustmentRequest->employee()->get()->first();
        $job = $buyerAdjustmentRequest->job()->first();

        $buyerAdjustmentRequest->status = 'denied';
        $buyerAdjustmentRequest->decision_date = Carbon::now();
        $buyerAdjustmentRequest->save();

        //Mail to employee
        /*Mail::send('emails.buyer_adjustment_request_denied_to_employee', ['job_title' => $job->title],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('Your request to modify the number of buyers has been denied');
        });*/

        //Mail to admin
        /*$admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin){
            Mail::send('emails.buyer_adjustment_request_denied_to_admin', ['job_title' => $job->title, 'employee_name' => $employee->full_name'],function($u) use ($admin, $employee)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($admin->email);
                $u->subject('You\'ve denied '. $employee->full_name . '\'s request to modify buyers');
            });
        }*/

        /*$buyers = $job->buyers()->get();
        foreach ($buyers as $buyer){
            Mail::send('emails.buyer_adjustment_request_denied_to_buyers', ['job_title'=>$job->title],function($u) use ($buyer, $job)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($buyer->email);
                $u->subject('No changes will be made to ' . $job->title);
            });
        }*/

        return response([
            'status' => 'success',
            'data' => null,
            'message' => null,
        ], 200);

    }

    private function getChangesArray($current_minimum, $current_maximum, $new_minimum, $new_maximum) {

	    $changes = array(
		    'min_change' => null,
		    'max_change' => null,
		    'new_minimum' => $new_minimum,
		    'new_maximum' => $new_maximum
	    );

	    if ($new_minimum > $current_minimum) {
		$changes['min_change'] = 'increase';
	    }
	    else if ($new_minimum < $current_minimum) {
		$changes['min_change'] = 'decrease';
	    }

	    if ($new_maximum > $current_maximum) {
		$changes['max_change'] = 'increase';
	    }
	    else if ($new_maximum < $current_maximum) {
		$changes['max_change'] = 'decrease';
	    }

	    return $changes;
    }
}
