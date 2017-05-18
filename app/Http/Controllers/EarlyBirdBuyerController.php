<?php

namespace App\Http\Controllers;

use App\EarlyBirdBuyer;
use App\Job;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use Illuminate\Support\Facades\Validator;

class EarlyBirdBuyerController extends Controller
{

	public function sendRequest(Request $request) {
		$user = Auth::user();

		$v = Validator::make($request->all(),[
			'employee_id' => 'required',
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
			$employee = User::where('id', $request->employee_id)->where('user_type', 'employee')->first();
			if (!$employee) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'employee with id ' . $request->employee_id . ' does not exist',
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

			$user->early_bird_buyers()->create($request->all());
			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}

	public function confirmRequest(Request $request) {
		$v = Validator::make($request->all(),[
			'early_bird_buyer_id' => 'required',
			'status' => 'required:requested,denied,working,ended',
		]);

		if ($v->fails()) {
			return response([
				'status' => 'error',
				'data' => $v->errors(),
				'message' => 'validation failed',
			], 200);
		}
		else{
			$earlyBirdBuyer = EarlyBirdBuyer::find($request->early_bird_buyer_id);

			if (!$earlyBirdBuyer) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'EarlyBirdBuyer with id ' . $request->early_bird_buyer_id . ' does not exist',
				], 200);
			}

			$earlyBirdBuyer->status = $request->status;
			$earlyBirdBuyer->save();

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
			$earlyBirdBuyer = $user->early_bird_buyers()->where('id', $request->early_bird_buyer_id)->where('status', 'requested')->first();

			if (!$earlyBirdBuyer) {
				return response([
					'status' => 'error',
					'data' => NULL,
					'message' => 'EarlyBirdBuyer with id ' . $request->early_bird_buyer_id . ' does not exist',
				], 200);
			}

			$earlyBirdBuyer->delete();

			return response([
				'status' => 'success',
				'data' => null,
				'message' => null,
			], 200);
		}
	}

	public function denyRequest() {

	}

	private function startEarlyBird() {

	}

	private function stopEarlyBird() {

	}
}
