<?php

namespace App\Http\Controllers;

use App\Category;
use App\EmployeeRequest;
use App\Job;
use App\MaintenanceWarning;
use App\PageText;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;

class PagesAdminController extends Controller
{
    public function users(){
        $users = User::all();
        return view('pages.admin.users', compact('users'));
    }

    public function cards(){
        $allCards = Job::all();
        /*$cards = $cards->sortByDesc(function($cards){
            return $cards->employee_requests_count;
        });*/
        $cardsWithRequests = $allCards->filter(function ($card) {
            return $card->employee_requests_count > 0;
        });

        $cardsWithRequests = $cardsWithRequests->sortByDesc(function($card) {
            // return sprintf('%-12s%-12s%s', $card->employee_requests_count, $card->sales_count, $card->employees_count);
            return $card->id;
        });

        $cardsWithoutRequests = $allCards->filter(function ($card) {
            return $card->employee_requests_count == 0;
        });

        $cardsWithoutRequests = $cardsWithoutRequests->sortByDesc(function($card) {
            // return sprintf('%-12s%-12s%s', $card->employee_requests_count, $card->sales_count, $card->employees_count);
            return $card->id;
        });

        $cards = $cardsWithRequests->merge($cardsWithoutRequests);




        /*$cards = $cards->sortByDesc(function($card) {
           // return sprintf('%-12s%-12s%s', $card->employee_requests_count, $card->sales_count, $card->employees_count);
            return sprintf('%-12s%-12s', $card->employee_requests_count, $card->id);
        });*/

        /*$cards->sort(
            function ($a, $b) {
                // sort by column1 first, then 2, and so on
                return strcmp($a->employee_requests_count, $b->employee_requests_count)
                    ?: strcmp($a->employees_count, $b->employees_count);
            }
        );*/

        return view('pages.admin.cards', compact('cards'));
    }

    public function categories(){
        $categories = Category::all();
        return view('pages.admin.categories', compact('categories'));
    }

    public function employee_requests($job_id){
        $job = Job::findOrFail($job_id);
        $employeeRequests = $job->employee_requests()->get();
        return view('pages.admin.employee_requests', compact('employeeRequests'));
    }

    public function orders($job_id){
        $job = Job::findOrFail($job_id);
        $orders = $job->sales()->get();
        return view('pages.admin.orders', compact('orders'));
    }

    public function texts(){
        $texts = PageText::all();
        return view('pages.admin.texts', compact('texts'));
    }

    public function maintenance_warnings(){
        $warnings = MaintenanceWarning::all();
        return view('pages.admin.maintenance_warnings', compact('warnings'));
    }
}
