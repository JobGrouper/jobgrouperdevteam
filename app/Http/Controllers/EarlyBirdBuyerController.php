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

use App\Operations\StartNewEarlyBirdOP;
use App\Operations\StopEarlyBirdOP;

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

			// send mail to employee
			Mail::queue('emails.early_bird_buyers_new_request_to_employee', ['user' => $user, 'job' => $job, 'employee' => $employee], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('New Early Bird Request');
			});
			// send mail to buyer
			Mail::queue('emails.early_bird_buyers_new_request_to_buyer', ['job' => $job, 'employee' => $employee], function($u) use ($user)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($user->email);
				$u->subject('Early Bird Request Sent');
			});

			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}

	public function confirmRequest(Request $request, StartNewEarlyBirdOP $op) {

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

			/*
			 * Handle this within the operations
			 *
			$earlyBirdBuyer->status = 'working';
			$earlyBirdBuyer->save();
			 */

			$job = $earlyBirdBuyer->job()->first();
			$new_markup = $job->early_bird_markup;
			$buyer = $earlyBirdBuyer->user()->first();

			$op->go($job, $earlyBirdBuyer);

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
			Mail::queue('emails.early_bird_buyers_request_cancelled_to_employee', ['data' => ['buyer' => $user, 'job' => $job]], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('Early Bird Request Cancelled');
			});
			// send mail to buyer
			Mail::queue('emails.early_bird_buyers_request_cancelled_to_buyer', ['data' => ['user' => $user, 'job' => $job]], function($u) use ($user)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($user->email);
				$u->subject('Early Bird Request Cancelled');
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
			$buyer = $earlyBirdBuyer->user()->first();

			// send mail to employee
			Mail::queue('emails.early_bird_buyers_request_denied_to_employee', ['buyer' => $buyer, 'job' => $job], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('Early Bird Request Denied');
			});

			// send mail to buyer
			Mail::queue('emails.early_bird_buyers_request_denied_to_buyer', ['buyer' => $buyer, 'job' => $job], function($u) use ($buyer)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($buyer->email);
				$u->subject('Early Bird Request Denied');
			});

			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}

	public function stopEarlyBird(Request $request, StopEarlyBirdOP $stop_early_bird) {

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
			$earlyBirdBuyer = EarlyBirdBuyer::where('id', $request->early_bird_buyer_id)->first();

			if (!$earlyBirdBuyer) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'EarlyBirdBuyer with id ' . $request->early_bird_buyer_id . ' does not exist',
				], 200);
			}

			/*
			$earlyBirdBuyer->status = 'denied';
			$earlyBirdBuyer->save();
			 */

			$job = $earlyBirdBuyer->job()->first();
			$employee = $job->employee->first();
			$buyer = $earlyBirdBuyer->user->first();

			$stop_early_bird->go($job, $earlyBirdBuyer);

			// send mail to employee
			Mail::queue('emails.early_bird_buyers_stopped_to_employee', ['data' => ['buyer' => $buyer, 'job' => $job]], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('Early Bird Ended');
			});
			// send mail to buyer
			Mail::queue('emails.early_bird_buyers_stopped_to_buyer', ['data' => ['job' => $job]], function($u) use ($buyer)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($buyer->email);
				$u->subject('Early Bird Ended');
			});

			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}
}
