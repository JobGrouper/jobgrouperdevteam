<?php

namespace App\Http\Controllers;

use App\BuyerAdjustment;
use App\BuyerAdjustmentRequest;
use App\Job;
use App\User;
use Illuminate\Http\Request;

use App\Operations\AdjustAllEarlyBirdsOP;

use App\Http\Requests;
use Auth;
use Validator;
use Mail;
use Carbon\Carbon;
use App\Interfaces\PaymentServiceInterface;

class BuyerAdjustmentController extends Controller
{
    public function create(Request $request, PaymentServiceInterface $psi, AdjustAllEarlyBirdsOP $adjust_all){

	// 
	// VALIDATION
	//
        $v = Validator::make($request->all(),[
                'request_id' => 'required_without:job_id|numeric',
                'job_id' => 'required_without:request_id|numeric',
                'new_client_max' => 'required|numeric',
                'new_client_min' => 'required|numeric',
            ]
        );

        $v->after(function($v) use ($request) {
    
            if ($request->new_client_max < $request->new_client_min) {
                $v->errors()->add('new_client_max', 'Maximum number of buyers cannot be less than the minimum');
            }
        });

        if($v->fails()){
            return ['status' => 0, 'data' => $v->errors(), 'message' => 'validator_error'];
        }
        
	//
	// MAKE CHANGES TO THE JOB
	//
        $job = Job::findOrFail($request->job_id);
        $employee = $job->employee()->first();
        $buyerAdjustmentRequest = null;
	$buyerAdjustment = null;

        if($request->request_id){
            $buyerAdjustmentRequest = BuyerAdjustmentRequest::findOrFail($request->request_id);

            $buyerAdjustmentRequest->status = 'accepted';
            $buyerAdjustmentRequest->decision_date = Carbon::now();
            $buyerAdjustmentRequest->save();
	}

	$changes = $this->getChangesArray($job->min_clients_count, $job->max_clients_count,
	$request->new_client_min, $request->new_client_max, $buyerAdjustmentRequest);

        $job->min_clients_count = $request->new_client_min;
        $job->max_clients_count = $request->new_client_max;
        $job->save();

	//
	// SAVE ADJUSTMENTS
	//
	$adjustment_data = [
                'job_id' => $job->id,
                'from_request_id' => NULL,
		'employee_id' => NULL,
                'old_client_min' => $job->min_clients_count,
                'old_client_max' => $job->max_clients_count,
                'new_client_min' => $request->new_client_min,
                'new_client_max' => $request->new_client_max,
            ];

        if($request->request_id) {
            $adjustment_data['from_request_id'] = $request->request_id; 
	}

	if($employee) {
	    $adjustment_data['employee_id'] = $employee->id;
	}

        $buyerAdjustment = BuyerAdjustment::create($adjustment_data);

        //if card has enough count of buyers and sellers the work begins
        if($job->sales_count >= $job->min_clients_count && null != $job->employee_id && $job->status != 'working'){

            $employee = $job->employee()->first();
            $psi->createPlan($employee, $job);
	    
	    return ['status' => 'success', 'message' => 'Adjustment successful, work is beginning'];
        }
	else if($job->sales_count < $job->min_clients_count && 
		$job->employee_id != NULL && $job->status != 'working' &&
		$changes['min_change'] != NULL) {

		// Adjust all Early Birds
		$adjust_all->go($job);
	}

	//
	// SEND NOTIFICATIONS
	//
	if ($employee) {

		if($request->request_id) {

		    Mail::queue('emails.buyer_adjustment_request_approved_to_employee', ['job_title'=>$job->title, 'job_id' => $job->id, 'changes' => $changes],function($u) use ($employee)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to($employee->email);
			$u->subject('Requested buyer adjustment approved');
		    });
		}
		else {

		    //Mail for employee
		    Mail::queue('emails.buyer_adjustment_made_to_employee', ['job_title'=>$job->title, 'job_id' => $job->id, 'changes' => $changes],function($u) use ($employee, $job)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to($employee->email);
			$u->subject('Number of buyers on your job: ' . $job->title . ' has changed');
		    });

		}
	}

        //$admins = User::where('role', 'admin')->get();
       // foreach ($admins as $admin){
	    //Mail to admin
	    Mail::queue('emails.buyer_adjustment_made_to_admin', ['job_title'=>$job->title],function($u) use ($job)
	    {
		$u->from('admin@jobgrouper.com');
		$u->to('admin@jobgrouper.com');
		$u->subject('Number of buyers on ' . $job->title . ' modified successfully');
	    });
        //}


        $buyers = $job->buyers()->distinct()->get();
        foreach ($buyers as $buyer) {

       		if($request->request_id) {

		    //Mail to buyers 
		    Mail::queue('emails.buyer_adjustment_request_approved_to_buyers', ['job_title'=>$job->title, 'job_id' => $job->id, 'employee_name' => $employee->full_name, 'employee_first_name' => $employee->first_name, 'changes' => $changes],function($u) use ($buyer, $job)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to($buyer->email);
			$u->subject('The number of buyers for ' . $job->title . ' has been modified');
		    });
		}
		else {

		    //Mail to buyers 
		    Mail::queue('emails.buyer_adjustment_made_to_buyers', ['job_title'=>$job->title, 'job_id' => $job->id, 'changes' => $changes],function($u) use ($buyer, $job)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to($buyer->email);
			$u->subject('The number of buyers for ' . $job->title . ' has been modified');
		    });

		}
        }

	return ['status' => 'success', 'message' => 'Adjustment successful'];
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

	$v->after(function($validator) use ($request) {

		if ($request->new_client_max < $request->new_client_min) {
			$validator->errors()->add('new_client_max', 'Maximum number of buyers cannot be less than the minimum');
		}
	});

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
        //$admins = User::where('role', 'admin')->get();
        //foreach ($admins as $admin){
            Mail::queue('emails.buyer_adjustment_request_to_admin', ['job_title'=>$job->title, 'employee_name' =>$employee->full_name, 'changes' => $changes],function($u)
            {
                $u->from('admin@jobgrouper.com');
                $u->to('admin@jobgrouper.com');
                $u->subject('Request to modify buyers');
            });
        //}

        //Mail for employee
        Mail::queue('emails.buyer_adjustment_request_to_employee', ['job_title'=>$job->title],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('Your request has gone through');
        });

        //Mail for buyers
        $buyers = $job->buyers()->distinct()->get();
        foreach ($buyers as $buyer){
            Mail::queue('emails.buyer_adjustment_request_to_buyers', ['job_title'=>$job->title, 'employee_name' => $employee->full_name, 'changes' => $changes],function($u) use ($buyer, $job)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($buyer->email);
                $u->subject($job->title . ' may be modified soon');
            });
        }


	    return response([
            'status' => 'OK',
            'data' => null,
            'message' => 'Buyer Request sent',
	    ], 200);
    }

    public function requestStartWorkNow(Request $request) {

        $employee = Auth::user();
        $job = Job::findOrFail($request->job_id);
        $purchases = $job->purchases();

        if (count($purchases) <= 0) {
            return response([
                'status' => 'X',
                'data' => null,
                'message' => 'Cannot start work without buyers attached',
            ], 200);
        }
        
        $employee->buyerAdjustmentRequests()->create([
            'job_id' => $request->job_id,
            'employee_id' => $request->employee_id,
            'current_client_max' => $request->current_client_max,
            'current_client_min' => $request->current_client_min,
            'requested_client_max' => $request->new_client_max,
            'requested_client_min' => count($purchases)
        ]);

	    $changes = $this->getChangesArray($request->current_client_min, $request->current_client_max,
		count($purchases), $request->new_client_max);

        //Mail for admin
        //$admins = User::where('role', 'admin')->get();
        //foreach ($admins as $admin){
            Mail::queue('emails.buyer_adjustment_request_start_work_to_admin', ['job_title'=>$job->title, 'employee_name' =>$employee->full_name, 'changes' => $changes],function($u)
            {
                $u->from('admin@jobgrouper.com');
                $u->to('admin@jobgrouper.com');
                $u->subject('Request to start work now');
            });
        //}

        //Mail for employee
        Mail::queue('emails.buyer_adjustment_request_start_work_to_employee', ['job_title'=>$job->title],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('Your request has gone through');
        });

        //Mail for buyers
        $buyers = $job->buyers()->distinct()->get();
        foreach ($buyers as $buyer){
            Mail::queue('emails.buyer_adjustment_request_start_work_to_buyers', ['job_title'=>$job->title, 'employee_name' => $employee->full_name, 'changes' => $changes],function($u) use ($buyer, $job)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($buyer->email);
                $u->subject($job->title . ' may start work soon');
            });
        }


	    return response([
            'status' => 'OK',
            'data' => null,
            'message' => 'Buyer Request sent',
	    ], 200);
    }

    public function startWorkNow(Request $request,  PaymentServiceInterface $psi){

        $v = Validator::make($request->all(),[
                'job_id' => 'required|numeric',
            ]
        );

        $v->after(function($validator) use ($request) {

            if ($request->new_client_max < $request->new_client_min) {
                $validator->errors()->add('new_client_max', 'Maximum number of buyers cannot be less than the minimum');
            }
        });

        if ($v->fails())
        {
            return response([
                'status' => 'X',
                'data' => $v->errors(),
                'message' => 'Failed, check error object',
            ], 200);
        }

        $job = Job::findOrFail($request->job_id);
        $employee = $job->employee()->first();

        if($job->sales_count == 0){
            return response([
                'status' => 'X',
                'data' => null,
                'message' => 'Failed, job does not have any buyers',
            ], 200);
        }

        if($job->status == 'working'){
            return response([
                'status' => 'X',
                'data' => null,
                'message' => 'Failed, work already started',
            ], 200);
        }

        if($request->request_id){

            $buyerAdjustmentRequest = BuyerAdjustmentRequest::findOrFail($request->request_id);
            $job = $buyerAdjustmentRequest->job()->get()->first();
	        $employee = $buyerAdjustmentRequest->employee()->first();

	    $buyerAdjustmentRequest->status = 'accepted';
	    $buyerAdjustmentRequest->decision_date = Carbon::now();
	    $buyerAdjustmentRequest->save();
	}

	$buyerAdjustment = BuyerAdjustment::create([
		'job_id' => $job->id,
		'old_client_min' => $job->min_clients_count,
		'old_client_max' => $job->max_clients_count,
		'new_client_min' => $job->sales_count,
		'new_client_max' => $job->max_clients_count,
	]);

  	$changes = $this->getChangesArray($request->current_client_min, $request->current_client_max,
		$job->sales_count, $request->new_client_max);

        $job->min_clients_count = $job->sales_count;
        $job->save();

	if (!$request->request_id) {

		//Mail for admin
		//$admins = User::where('role', 'admin')->get();
		//foreach ($admins as $admin){

		    //Mail to admin
		    Mail::queue('emails.buyer_adjustment_starting_work_now_to_admin', ['job_title'=>$job->title],function($u) use ($job)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to('admin@jobgrouper.com');
			$u->subject('Number of buyers on ' . $job->title . ' modified successfully');
		    });
		//}

		//Mail for employee
		Mail::queue('emails.buyer_adjustment_starting_work_now_to_employee', ['job_title'=>$job->title, 'job_id' => $job->id, 'changes'=> $changes],function($u) use ($employee, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('Our admin has decided to start work on ' . $job->title . ' immediately');
		});

		//Mail for buyers
		$buyers = $job->buyers()->distinct()->get();
		foreach ($buyers as $buyer){

		    Mail::queue('emails.buyer_adjustment_starting_work_now_to_buyers', ['job_title'=>$job->title, 'employee_name' => $employee->full_name, 'employee_first_name' => $employee->first_name, 'changes' => $changes],function($u) use ($buyer, $job)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to($buyer->email);
			$u->subject('Our admin has decided to start work on ' . $job->title . ' immediately');
		    });
		}
	}

        //if card has enough count of buyers and sellers the work begins
        if($job->sales_count >= $job->min_clients_count && null != $job->employee_id && $job->status != 'working'){

            $psi->createPlan($employee, $job);
        }

        return response([
            'status' => 'OK',
            'data' => null,
            'message' => 'Work started',
        ], 200);
    }

    public function deny_request($requestID){
        $buyerAdjustmentRequest = BuyerAdjustmentRequest::findOrFail($requestID);
        if($buyerAdjustmentRequest->status != 'pending'){
            return response([
                'status' => 0,
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
        Mail::queue('emails.buyer_adjustment_request_denied_to_employee', ['job_title' => $job->title],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('Your request to modify the number of buyers has been denied');
        });

        //Mail to admin
        //$admins = User::where('role', 'admin')->get();
        //foreach ($admins as $admin){
            Mail::queue('emails.buyer_adjustment_request_denied_to_admin', ['job_title' => $job->title, 'employee_name' => $employee->full_name], function($u) use ($employee)
            {
                $u->from('admin@jobgrouper.com');
                $u->to('admin@jobgrouper.com');
                $u->subject('You\'ve denied '. $employee->full_name . '\'s request to modify buyers');
            });
        //}

        $buyers = $job->buyers()->distinct()->get();
        foreach ($buyers as $buyer){
            Mail::queue('emails.buyer_adjustment_request_denied_to_buyers', ['job_title'=>$job->title],function($u) use ($buyer, $job)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($buyer->email);
                $u->subject('No changes will be made to ' . $job->title);
            });
        }

        return response([
            'status' => 'success',
            'data' => null,
            'message' => null,
        ], 200);

    }

    private function getChangesArray($current_minimum, $current_maximum, $new_minimum, $new_maximum, $request=NULL) {

	    $changes = array(
		    'min_change' => null,
		    'max_change' => null,
		    'current_minimum' => $current_minimum,
		    'current_maximum' => $current_maximum,
		    'new_minimum' => $new_minimum,
		    'new_maximum' => $new_maximum,
		    'request_modified' => false,
		    'request_min_modified' => false,
		    'request_max_modified' => false
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

	    if ($request) {

		    if ($new_minimum != $request->requested_client_min) {
			$changes['request_modified'] = true;
			$changes['request_min_modified'] = true;
		    }

		    if ($new_minimum != $request->requested_client_min) {
			$changes['request_modified'] = true;
			$changes['request_max_modified'] = true;
		    }
	    }

	    return $changes;
    }
}
