<?php

namespace App\Http\Controllers;

use App\Category;
use App\Job;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{
    public function home(){

        // get user
        $user = Auth::user();

        //In the top of list are the hot cards (first of them in is in slider)
        $jobs = Job::notDummy()->hot()->get();

        //Next will take place the not hot cards with buyers above 25%.
        $notHotJobs = Job::notDummy()->where('hot', false)->get();
        $notHotJobsAbove25 =  $notHotJobs->filter(function ($job) {
            return $job->sales_percent >= 25;
        })->sortByDesc('sales_percent');

        //Next will be other cards sorted by date of creation
        $notHotJobsOther =  $notHotJobs->filter(function ($job) {
            return $job->sales_percent < 25;
        })->sortByDesc('created_at');

        $jobs = $jobs->merge($notHotJobsAbove25);
        $jobs = $jobs->merge($notHotJobsOther);

        //Filtering out jobs with max buyers and employee
        $jobs =  $jobs->filter(function ($job) {
            return ($job->sales_count < $job->max_clients_count || $job->employee == null);
        });

        return view('pages.main', ['user' => $user, 'jobs' => $jobs]);
    }

    public function zhTest() {

	return view('pages.zh', ['user' => NULL, 'jobs' => NULL]);
    }


    public function help(){
        return view('pages.help');
    }

    public function terms(){
        return view('pages.terms');
    }
}
