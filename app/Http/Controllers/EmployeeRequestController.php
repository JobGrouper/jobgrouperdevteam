<?php

namespace App\Http\Controllers;

use App\EmployeeExitRequest;
use App\EmployeeRequest;
use App\Job;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Mail; // фасад для отправки почты


use App\Http\Requests;

class EmployeeRequestController extends Controller
{
    /**
     * Creating new request from employee to aply the job card
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request){
        $responseData = array();

        $employee = Auth::user();

        //Check if user is employee
        if($employee->user_type != 'employee'){
            $responseData['error'] = true;
            $responseData['status'] = 1;
            $responseData['info'] = 'User is not employee';
            return response($responseData, 200);
        }


        $job = Job::findOrFail($request->job_id);

        //Check if user has not already make request to this card
        /*if (EmployeeRequest::where('employee_id', '=', $employee->id)->where('job_id', '=', $request->job_id)->count() > 0) {
            $responseData['error'] = true;
            $responseData['status'] = 2;
            $responseData['info'] = 'You already have done request to this job';
            return response($responseData, 200);
        }*/


        EmployeeRequest::where('employee_id', '=', $employee->id)->where('job_id', '=', $request->job_id)->delete();

        $this->validate($request, [
            'job_id' => 'job_id'
        ]);



        $EmployeeRequest = $employee->employee_requests()->create([
            'job_id' => $request->job_id,
        ]);

        if(!$EmployeeRequest->id){
            $responseData['error'] = true;
            $responseData['status'] = -1;
            $responseData['info'] = 'Something went wrong. Please try again later.';
            return response($responseData, 500);
        }


        $responseData['request_id'] = $EmployeeRequest->id;
        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['info'] = 'Request successfully created';

        Mail::send('emails.new_employee_request',['job_name'=>$job->title, 'employee_name'=>$employee->full_name],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('New Employee request!');
        });

        return response($responseData, 200);

    }

    /**
     * Approving employee`s request by admin
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */

    public function approve(Request $request){
        $employeeRequest = EmployeeRequest::where('id', '=', $request->employee_request_id)->first();
        if($employeeRequest->status != 'approved'){
            $employeeRequest->status = 'approved';
            $employeeRequest->save();
            $job = $employeeRequest->job()->first();


            //Есди в работе есть работник, который покидает работу, делаем нового заявщика потенциальным
            if($job->employee_status['status'] == 'leave'){
                $job->potential_employee_id = $employeeRequest->employee_id;
            }
            else{
                $job->employee_id = $employeeRequest->employee_id;
            }


            //if card has enough count of buyers and sellers the work begins
            if($job->sales_count == $job->max_clients_count){
                $job->work_start();
            }
            $job->save();

            $employee = $job->employee()->first();
            Mail::send('emails.employee_request_approved',['job_name'=>$job->title],function($u) use ($employee)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($employee->email);
                $u->subject('Request approved!');
            });
        }
        return redirect('/admin/employee_requests/'.$job->id);
    }

    /**
     * Rejecting employee`s request by admin
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function reject(Request $request){
        $employeeRequest = EmployeeRequest::where('id', '=', $request->employee_request_id)->first();
        EmployeeExitRequest::where('employee_id', '=', $employeeRequest->employee_id)->delete();
        if($employeeRequest->status != 'rejected') {
            $employeeRequest->status = 'rejected';
            $employeeRequest->save();
            $job = $employeeRequest->job()->first();
            $employee = $job->employee()->first();
            //Если пользователь еще не выполнитель этой карточни, отправляем письмо, что заявка отклонена
            if($job->employee_id != $employeeRequest->employee_id){
                Mail::send('emails.employee_request_rejected', ['job_name' => $job->title], function ($u) use ($employee) {
                    $u->from('admin@jobgrouper.com');
                    $u->to($employee->email);
                    $u->subject('Request rejected!');
                });
            }
            //Если пользователь уже выполнитель этой карточни, отправляем письмо, что отстранен от работы
            else{
                Mail::send('emails.discharged_of_work', ['job_name' => $job->title], function ($u) use ($employee) {
                    $u->from('admin@jobgrouper.com');
                    $u->to($employee->email);
                    $u->subject('You was discharged of work!');
                });
            }
            $job->employee_id = null;

            $job->save();

            $job->work_stop();
        }
        return redirect('/admin/employee_requests/'.$job->id);
    }

    public function destroy($employee_request_id){
        $responseData = array();

        $employeeRequest = EmployeeRequest::find($employee_request_id);
        if($employeeRequest->status == 'pending'){
            $employeeRequest->delete();
            $responseData['error'] = false;
            $responseData['status'] = 0;
            $responseData['info'] = 'Request successfully canceled';
        }

        return response($responseData, 200);

    }

    public function getStatus($id){
        $responseData = array();

        $employeeRequest = EmployeeRequest::findOrFail($id);

        $responseData['error'] = false;
        $responseData['status'] = $employeeRequest->status;

        return response($responseData, 200);
    }

}
