<?php

namespace App\Http\Controllers;

use App\Category;
use App\Job;
use App\Http\Requests;

class PagesController extends Controller
{
    public function home(){
        //In the top of list are the hot cards (first of them in is in slider)
        $jobs = Job::hot()->get();

        //Next will take place the not hot cards with buyers above 25%.
        $notHotJobs = Job::where('hot', false)->get();
        $notHotJobsAbove25 =  $notHotJobs->filter(function ($job) {
            return $job->sales_percent >= 25;
        })->sortByDesc('sales_percent');

        //Next will be other cards sorted by date of creation
        $notHotJobsOther =  $notHotJobs->filter(function ($job) {
            return $job->sales_percent < 25;
        })->sortByDesc('created_at');

        $jobs = $jobs->merge($notHotJobsAbove25);
        $jobs = $jobs->merge($notHotJobsOther);
        $chunks = $jobs->chunk(9); //9 = 1 in slider + 8 in rows

        return view('pages.main', ['jobs' => $chunks->first());
    }


    public function help(){
        return view('pages.help');
    }

    public function terms(){
        return view('pages.terms');
    }
}
