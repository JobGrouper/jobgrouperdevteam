<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\User;
class UserController extends Controller
{
    public function myAccount(){
        $user = Auth::user();
        return view('pages.account', ['user' => $user]);
    }

    public function showAccount($userID){
        $user = User::findOrFail($userID);
        /*if(Auth::user()->id == $userID){
            return redirect('/account');
        }*/
        return view('pages.employee_profile', ['user' => $user]);
    }

    public function update(Request $request){
        $response = array();

        $input = $request->only(['first_name', 'last_name', 'linkid_url', 'fb_url', 'git_url', 'paypal_email']);
        $user = Auth::user();
        //$user = User::where('email', '=', 'ovch2008@ukr.net')->first();
        $user->fill($request->all());
        $user->save();

        if(isset($request->image_hash)){
            $imageHash = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image_hash));
            $imageName = 'users/u_'.$user->id.'.png';
            if(!file_put_contents(config('app.images_dir').$imageName, $imageHash)){
                $responseData['error'] = true;
                $responseData['status'] = -1;
                $responseData['info'] = 'Something went wrong. Please try again later.';
                return response($responseData, 500);
            }
        }

        $response['errors'] = false;
        $response['status'] = 0;
        $response['info'] = 'User data successfully updated!';

        return response($response, 200);
    }


    public function showJobs(){
        $employee = Auth::user();
        if($employee->user_type != 'employee'){
            return redirect('/');
        }

        $jobs = $employee->jobs()->get();
        $potentialJobs = $employee->potential_jobs()->get();
        $employeeRequests = $employee->employee_requests()->where('status', 'pending')->get();

        return view('pages.account.my_jobs', compact('jobs', 'potentialJobs', 'employeeRequests'));
    }

    public function showOrders(){
        $buyer = Auth::user();
        if($buyer->user_type != 'buyer'){
            return redirect('/');
        }

        $orders = $buyer->orders()->where('status', 'in_progress')->get();
  

        return view('pages.account.my_orders', ['orders' => $orders]);
    }

    public function showPayments(){
        $buyer = Auth::user();
        if($buyer->user_type != 'buyer'){
            return redirect('/');
        }

        $payments = $buyer->payments()->get();


        return view('pages.account.payments_history', compact('payments'));
    }

    public function deactivate($user_id){
        $user = User::findOrFail($user_id);
        if($user->role != 'admin'){
            if($user->active){
                $user->update(['active' => false]);
                die('deactivated');
            }
            else{
                $user->update(['active' => true]);
                die('activated');
            }
        }
    }
}
