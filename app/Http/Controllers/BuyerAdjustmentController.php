<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use Validator;
use Mail;

class BuyerAdjustmentController extends Controller
{
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
        $admin = User::where('role', 'admin')->get()->first();
        Mail::send('emails.buyer_adjustment_request_to_admin', ['job_title'=>$job->title],function($u) use ($admin)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($admin->email);
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
        $buyers_emails = array_values($job->buyers()->get()->pluck('email')->toArray());
        //dd($buyers_emails);
        Mail::send('emails.buyer_adjustment_request_to_buyers', ['job_title'=>$job->title],function($u) use ($buyers_emails)
        {
            $u->from('admin@jobgrouper.com');
            $u->to(['ovch2009@ukr.net','ovch2008@ukr.net']);
            $u->subject('Changing max  number of buyers');
        });

        return redirect()->back()->with('message', 'success');
    }
}
