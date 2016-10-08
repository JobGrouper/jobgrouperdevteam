<?php

namespace App\Http\Controllers;

use App\Category;
use App\Job;
use App\Http\Requests;

class PagesController extends Controller
{
    public function home(){
        //$jobs = Job::where('employees_count', '=', 0)->orderBy('id', 'desc')->paginate(8);
        //$jobs = Job::all();
        $categories = Category::all();
        $categories->prepend(new Category(['title' => 'All Categories']));

       /*$jobs = $jobs->filter(function($job)
        {
            return !($job->sales_count == $job->max_clients_count && $job->employees_count > 0);
        });

        $jobs = $jobs->sortByDesc('become_hot');

        $chunks = $jobs->chunk(8);



        $hotJobs = Job::hot()->get();*/

        //Первыми на главной странице идут хоты (первый из них в слайдере)
        //Затем идут просто карточки по новизне.
        $jobs = Job::hot()->get();
        $notHotJobs = Job::where('hot', false)->get()->sortByDesc('id');
        $jobs = $jobs->merge($notHotJobs);
        $chunks = $jobs->chunk(9);
        return view('pages.main', ['categories' => $categories, 'jobs' => $chunks->first()/*, 'hotJobs' => $hotJobs*/]);
    }


    public function help(){
        return view('pages.help');
    }

    public function terms(){
        return view('pages.terms');
    }
}
