<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Requests;

use App\Job;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class JobController extends Controller
{
    /**
     * Creating ne job-card
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(){
        $categories = Category::all();
        return view('pages.admin.card', ['categories' => $categories]);
    }


    /**
     * Fetching all jobs
     *
     * @param  Request  $request
     * @return Response
     */
    public function all($categoryID = 0){
        $pageUrl = '';
        if($categoryID){
            $category = Category::findOrFail($categoryID);
            $jobs = $category->jobs()->paginate(12);
            $categoryTitle = $category->title;
            $pageUrl = '/jobs/category/'.$category->id;
        }
        else{
            $jobs = Job::where('id', '>', '0')->paginate(12);
            $categoryTitle = 'All categories';
            $pageUrl = '/jobs';
        }

        return view('pages.jobs.jobs', ['pageUrl' => $pageUrl, 'categoryTitle' => $categoryTitle, 'jobs' => $jobs]);
    }
    

    public function show($jobID){
        $employee = 0;
        $emploeeStatus = 0;
        $sales = 0;
        $employeeRequest = 0;
        $job = Job::findOrFail($jobID);

        $category = $job->category()->get()->first();
        if($job->employee_id){
            $employee = $job->employee()->first();
            $emploeeStatus = $job->employee_status;
        }

        $user = Auth::user();
		$user_order_info = null;
        $jobOrdered = false;
        $jobPaid = false;

        if($user){
            if($user->user_type == 'employee'){
                if($user->employee_requests()->where('job_id', '=', $job->id)->count()){
                    $employeeRequest = $user->employee_requests()->where('job_id', '=', $job->id)->first();
                }
            }
            else{

				// Retrieving user info
				$user_order_info = $user->orders()->where('job_id', '=', $job->id)->where('status', '=', 'in_progress')->first();

                if($user_order_info){

                    $jobOrdered = true;

					// If credit card id is present, job has been paid for
					//
					if ($user_order_info->credit_card_id != null) {
						$jobPaid = true;
					}
                }
            }
        }



        //$sales = $job->buyers()->get();
        $orders = $job->sales()->where('status', '=', 'in_progress')->get();

		if ($jobOrdered && !$jobPaid) {

		}

        return view('pages.jobs.job', ['job' => $job, 'category' => $category, 'employee' => $employee, 'employeeStatus' => $emploeeStatus, 'orders' => $orders, 'employeeRequest' => $employeeRequest, 'jobOrdered' => $jobOrdered, 'jobPaid' => $jobPaid, 'user_order_info' => $user_order_info]);
    }


    public function edit($jobID){

        $job = Job::findOrFail($jobID);
        $categories = Category::all();
        return view('pages.admin.card', ['job' => $job, 'categories' => $categories]);
    }
    /**
     * Create new job
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {


         $job = Job::create([
            'title' => $request->title,
            'description' => $request->description,
            'salary' => $request->salary,
            'max_clients_count' => $request->max_clients_count,
            'category_id' => $request->category_id,
        ]);


        if($request->image_hash){
            $imageHash = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image_hash));
            $imageName = 'jobs/j_'.$job->id.'.png';
            file_put_contents(config('app.images_dir').$imageName, $imageHash);

        }


        if(!$job->id){
            die('error');
        }

        return redirect('/admin/cards');
    }

    /**
     * Update job
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $job = Job::find($request->job_id);
        $hot = ($request->hot ? 1 : 0);

        $job->fill($request->all());
        if($hot){
            $job->hot = $hot;
            $job->become_hot = Carbon::now();
        }
        else{
            $job->hot = false;
            $job->become_hot = null;
        }
        $job->save();

        if($request->image_hash){
            $imageHash = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image_hash));
            $imageName = 'jobs/j_'.$job->id.'.png';
            file_put_contents(config('app.images_dir').$imageName, $imageHash);
        }

        return redirect('/admin/cards');
    }

    /**
     * Delete job
     *
     * @param  int $job_id
     */
    public function destroy($job_id)
    {
        $job = Job::findOrFail($job_id);
        $job->delete();
        $job = Job::find($job_id);

        if(!isset($job->id)){
            die('success');
        }
        else{
            die('fail');
        }
    }
}
