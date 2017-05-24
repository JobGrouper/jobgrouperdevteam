<?php

namespace App\Http\Controllers;

use App\EarlyBirdBuyer;
use App\Job;
use App\Sale;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use Illuminate\Support\Facades\Validator;
use Mail;

use \Carbon\Carbon;

class EarlyBirdBuyerController extends Controller
{

	public function sendRequest(Request $request) {
		$user = Auth::user();

		$v = Validator::make($request->all(),[
			'order_id' => 'required',
			'job_id' => 'required',
		]);

		if ($v->fails()) {
			return response([
				'status' => 'error',
				'data' => $v->errors(),
				'message' => 'validation failed',
			], 200);
		}
		else {
			//$sale = Sale::where('job_id', $request->job_id)->where('buyer_id', $user->id)->first();
			$sale = Sale::findOrFail($request->order_id);
			if (!$sale) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'sale for job with id ' . $request->job_id . ' and user id '.$user->id.' does not exist',
				], 200);
			}

			$job = Job::where('id', $request->job_id)->first();
			if (!$job) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'job with id ' . $request->job_id . ' does not exist',
				], 200);
			}

			$employee = $job->employee()->first();

			//$employee = User::where('id', $request->employee_id)->where('user_type', 'employee')->first();
			if (!$employee) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'employee with id ' . $request->employee_id . ' does not exist',
				], 200);
			}


			$user->early_bird_buyers()->create([
				'user_id' => $user->id,
				'employee_id' => $employee->id,
				'job_id' => $job->id,
				'sale_id' => $sale->id,
				'created_at' => Carbon::now()
			]);

			/*
			// send mail to employee
			Mail::send('emails.early_bird_buyers_new_request_to_employee', ['user' => $user, 'job' => $job], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('subject');
			});
			// send mail to buyer
			Mail::send('emails.early_bird_buyers_request_cenceled_to_buyer', ['user' => $user, 'job' => $job], function($u) use ($user)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($user->email);
				$u->subject('subject');
			});
			 */

			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}

	public function confirmRequest(Request $request) {
		$employee = Auth::user();

		$v = Validator::make($request->all(),[
			'early_bird_buyer_id' => 'required',
		]);

		if ($v->fails()) {
			return response([
				'status' => 'error',
				'data' => $v->errors(),
				'message' => 'validation failed',
			], 200);
		}
		else{
			$earlyBirdBuyer = EarlyBirdBuyer::where('id', $request->early_bird_buyer_id)
				->where('employee_id', $employee->id)
				->where('status', 'requested')
				->first();

			if (!$earlyBirdBuyer) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'EarlyBirdBuyer with id ' . $request->early_bird_buyer_id . ' does not exist',
				], 200);
			}

			$earlyBirdBuyer->status = 'working';
			$earlyBirdBuyer->save();

			$job = $earlyBirdBuyer->job()->first();
			$user = $earlyBirdBuyer->user()->first();

			// send mail to employee
			Mail::send('emails.early_bird_buyers_request_confirmed_to_employee', ['user' => $user, 'job' => $job], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('/*subject*/');
			});
			// send mail to buyer
			Mail::send('emails.early_bird_buyers_request_confirmed_to_buyer', ['user' => $user, 'job' => $job], function($u) use ($user)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($user->email);
				$u->subject('/*subject*/');
			});

			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}


	public function cancelRequest(Request $request) {
		$user = Auth::user();

		$v = Validator::make($request->all(),[
			'early_bird_buyer_id' => 'required',
		]);

		if ($v->fails()) {
			return response([
				'status' => 'error',
				'data' => $v->errors(),
				'message' => 'validation failed',
			], 200);
		}
		else {
			$earlyBirdBuyer = $user->early_bird_buyers()
				->where('id', $request->early_bird_buyer_id)
				->where('status', 'requested')
				->first();

			if (!$earlyBirdBuyer) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'EarlyBirdBuyer with id ' . $request->early_bird_buyer_id . ' does not exist',
				], 200);
			}

			$job = $earlyBirdBuyer->job()->first();
			$employee = $earlyBirdBuyer->employee()->first();


			/*
			 * TODO: Gonna figure this one out later in the interest of data storage
			 *
			$earlyBirdBuyer->status = 'cancelled';
			$earlyBirdBuyer->save();
			 */

			//
			// For now, we'll just...
			$earlyBirdBuyer->delete();

			// send mail to employee
			Mail::send('emails.early_bird_buyers_request_cenceled_to_employee', ['user' => $user, 'job' => $job], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('/*subject*/');
			});
			// send mail to buyer
			Mail::send('emails.early_bird_buyers_request_cenceled_to_buyer', ['user' => $user, 'job' => $job], function($u) use ($user)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($user->email);
				$u->subject('/*subject*/');
			});

			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}

	public function denyRequest(Request $request) {
		$employee = Auth::user();

		$v = Validator::make($request->all(),[
			'early_bird_buyer_id' => 'required',
		]);

		if ($v->fails()) {
			return response([
				'status' => 'error',
				'data' => $v->errors(),
				'message' => 'validation failed',
			], 200);
		}
		else{
			$earlyBirdBuyer = EarlyBirdBuyer::where('id', $request->early_bird_buyer_id)
				->where('employee_id', $employee->id)
				->where('status', 'requested')
				->first();

			if (!$earlyBirdBuyer) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'EarlyBirdBuyer with id ' . $request->early_bird_buyer_id . ' does not exist',
				], 200);
			}

			$earlyBirdBuyer->status = 'denied';
			$earlyBirdBuyer->save();

			$job = $earlyBirdBuyer->job()->first();
			$user = $earlyBirdBuyer->user()->first();

			/*
			// send mail to employee
			Mail::send('emails.early_bird_buyers_request_denied_to_employee', ['user' => $user, 'job' => $job], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('subject');
			});
			// send mail to buyer
			Mail::send('emails.early_bird_buyers_request_denied_to_buyer', ['user' => $user, 'job' => $job], function($u) use ($user)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($user->email);
				$u->subject('subject');
			});
			 */

			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}
}
