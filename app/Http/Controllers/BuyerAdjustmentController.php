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

            $buyerAdjustment = BuyerAdjustment::create([
                'from_request_id' => $request->request_id,
                'job_id' => $job->id,
                'old_client_min' => $job->min_clients_count,
                'old_client_max' => $job->max_clients_count,
                'new_client_min' => $request->new_client_min,
                'new_client_max' => $request->new_client_max,
            ]);


            $job->min_clients_count = $request->new_client_min;
            $job->max_clients_count = $request->new_client_max;
            $job->save();

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

            $job->min_clients_count = $request->new_client_min;
            $job->max_clients_count = $request->new_client_max;
            $job->save();

            if($buyerAdjustment->old_client_min >= $job->min_clients_count){
                // Starts plan
            }

            return ['status' => 'success'];
        }


    }

    public function create_request(Request $request){
        $v = Validator::make($request->all(),[
                'job_id' => 'required|numeric',
                'current_client_max' => 'required|numeric',
                'current_client_min' => 'required|numeric',
                'requested_client_min' => 'required|numeric',
                'requested_client_max' => 'required|numeric',

            ]
        );
        if ($v->fails())
        {
            return redirect()->back()->with($v->errors());
        }

        $employee = Auth::user();
        $job = Job::findOrFail($request->job_id);
        
        $employee->buyerAdjustmentRequests()->create($request->all());

        //Mail for admin
        $adminsEmails = User::where('role', 'admin')->get()->pluck('email')->toArray();
        Mail::send('emails.buyer_adjustment_request_to_admin', ['job_title'=>$job->title],function($u) use ($adminsEmails)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($adminsEmails);
            $u->subject('New buyer adjustment request');
        });

        //Mail for employee
        Mail::send('emails.buyer_adjustment_request_to_employee', ['job_title'=>$job->title],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('Your buyer adjustment has been sent');
        });

        //Mail for buyers
        $buyersEmails = array_values($job->buyers()->get()->pluck('email')->toArray());
        Mail::send('emails.buyer_adjustment_request_to_buyers', ['job_title'=>$job->title],function($u) use ($buyersEmails)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($buyersEmails);
            $u->subject('Changing max number of buyers');
        });

        return redirect()->back()->with('message', 'success');
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

        $buyerAdjustmentRequest->status = 'denied';
        $buyerAdjustmentRequest->decision_date = Carbon::now();
        $buyerAdjustmentRequest->save();

        //Mail to employee
        /*Mail::send('emails.buyer_adjustment_request_denied_to_employee', [],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('Your request has been denied');
        });*/

        //Mail to admin
        /*$adminsEmails = User::where('role', 'admin')->get()->pluck('email')->toArray();
        Mail::send('emails.buyer_adjustment_request_denied_to_admin', [],function($u) use ($adminsEmails)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($adminsEmails);
            $u->subject('Denial was successful');
        });*/

        return response([
            'status' => 'success',
            'data' => null,
            'message' => null,
        ], 200);

    }
}
