<?php

namespace App;
use Mail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use DB;

use Auth;

class Job extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'employee_id', 'potential_employee_id', 'title', 'title_ch', 'description', 'description_ch', 'salary', 'salary', 'min_clients_count', 'max_clients_count', 'hot', 'become_hot', 'next_payment_date', 'is_dummy'
    ];

    /**
     * The attributes that should be casted to native types.
     * The supported cast types are:
     * integer, real, float, double, string, boolean, object, array, collection, date, datetime, timestamp.
     *
     * @var array
     */
    protected $casts = [
        'max_clients_count' => 'int',
	'min_clients_count' => 'int'
    ];



    /**
     * Accessors
     */
    public function getShortTitleAttribute()
    {
        return (strlen($this->title) < 40 ? $this->title : substr($this->title, 0, 40).'...');
    }

    public function getShortDescriptionAttribute()
    {
        return (strlen($this->description) < 40 ? $this->description : substr($this->description, 0, 60).'...');
    }

    /**
     * Accessor to get in_progress orders count
     */
    public function getSalesCountAttribute()
    {
        $salesCount = $this->sales()->where('status', '=', 'in_progress')->count();
        return $salesCount;
    }

    /**
     * Accessor to get percent of busy places
     */
    public function getSalesPercentAttribute()
    {
        return $this->sales_count * 100 / $this->max_clients_count;
    }

    /**
     * Accessor to get employees count for the job
     */
    public function getEmployeesCountAttribute()
    {
        return $this->employee()->count();
    }

    /**
     * Accessor to get employees requests with status = pending for the job
     */
    public function getEmployeeRequestsCountAttribute()
    {
        return $this->employee_requests()->where('status', '=', 'pending')->count();
    }

    /**
     * Accessor to get current buyer adjustment request 
     */
    public function getCurrentBuyerAdjustmentRequestAttribute() 
    {
	    return $this->buyer_adjustment_requests()->where('status', 'pending')->
		    orderBy('created_at', 'desc')->first();
    }

    /**
     * Accessor to get buyer adjustment requests with status = pending
     */
    public function getBuyerAdjustmentRequestsCountAttribute()
    {
        return $this->buyer_adjustment_requests()->where('status', '=', 'pending')->count();
    }

    /**
     * Accessor to get image_url attribute
     */
    public function getImageUrlAttribute()
    {
        //return (file_exists('images/jobs/j_'.$this->id.'.png') ? 'images/jobs/j_'.$this->id.'.png' : 'images/jobs/j_1.png');
        return (file_exists('images/jobs/j_'.$this->id.'.png') ? 'images/jobs/j_'.$this->id.'.png' : 'img/Profile/user.png');
    }

    /**
     * Accessor to get monthly salary
     */
    public function getMonthlySalaryAttribute()
    {
        return $this->salary * $this->max_clients_count;
        //return $this->salary * $this->max_clients_count * 0.85;
    }

    /**
     * Accessor to get monthly price
     */
    public function getMonthlyPriceAttribute()
    {
        //return $this->salary;
        return ($this->salary + ($this->salary / 100 * 15));
    }


    /**
     * Accessor to get yearly salary
     */
    public function getYearlySalaryAttribute()
    {
        return $this->salary * $this->max_clients_count * 12;
    }

    /**
     * Accessor to get status of employ
     * stable - работник работает нормально
     * leave - уходит, возращаем дату ухода и potential_buyer если есть.
     * false - работника нет
     */
    public function getEmployeeStatusAttribute()
    {
        $employee = $this->employee()->first();
        if(isset($employee->id)){
            $employeeExitRequest = $employee->employee_exit_requests()->where('job_id', $this->id)->where('status', 'pending');
            if($employeeExitRequest->count()){
                $leave_date = $employeeExitRequest->first()->created_at->addWeeks(2);
                return [
                    'employe_id' => $employee->id,
                    'status' => 'leave',
                    'leave_date' => $leave_date->format("M, d Y")
                ];
            }
            else{
                return [
                    'employe_id' => $employee->id,
                    'status' => 'stable'
                ];
            }
        }

        return false;

    }

    public function getPreOrdersAttribute()
    {
        if($this->status == 'waiting'){
            return $this->sales()->where('status', '=', 'pending')->where('card_set', 0)->get()->count();
        }
    }

    public function getEarlyBirdMarkupAttribute() {
        $current_early_bird_count = $this->early_bird_buyers()->where('status', 'working')->get()->count();
        $min_clients_count = $this->min_clients_count;
        $surcharge = $this->salary * 0.15;
        $normal_total = ($this->salary + $surcharge);


	// Calculate the extra markup
	$xtra_markup = .15 * $this->salary;

	if ($current_early_bird_count > 0) {
        	$xtra_markup = $this->salary * (0.15 * ( 1 - ( $current_early_bird_count / $min_clients_count )));
	}

        $total_price_will_be = $normal_total + $xtra_markup;

	    return $total_price_will_be;
    }

    /*
     * Depending on level of user, returns appropriate "sale" number
     * Not an accessor
     * 
     */
    public function getConfiguredSale(User $user=NULL) {

	    if (!$user) {
		return number_format($this->salary, 2);
	    }
	    else {
		if ($user->user_type == 'buyer') {
			return number_format($this->salary, 2);
		}

		if ($user->user_type == 'employee') {
			return number_format($this->monthly_salary, 2);
		}
	    }
    }

    public function getMinClientsOrdinalAttribute() {

	    $ordinal = '' . $this->min_clients_count;

	    if ($this->min_clients_count == 1 || $this->min_clients_count == -1)
		    $ordinal .= 'st'; 
	    else if ($this->min_clients_count == 2 || $this->min_clients_count == -2) 
		    $ordinal .= 'nd'; 
	    else if ($this->min_clients_count == 3 || $this->min_clients_count == -3)
		    $ordinal .= 'rd'; 
	    else 
		    $ordinal .= 'th'; 

	    return $ordinal;
    }


    /**
     * Scope for hot jobs
     */
    public function scopeHot($query)
    {
        return $query->where('hot', '=', true)->orderBy('become_hot', 'desc');
    }




    /**
     * Make hot
     */

    public function make_hot(){
        $this->hot = true;
        $this->become_hot = Carbon::now();
        $this->save();
    }


    /**
     * Start work
     */

    public function work_start(){
        $this->status = 'working';
        $employee = $this->employee()->first();
        $this->status = 'working';
        $this->work_started_at = Carbon::now();
        $this->next_payment_date = Carbon::now()->addMonth();
        $this->hot = false;
        $this->save();

        //Notify for employee that work starting
        Mail::send('emails.job_work_start',['job_name'=>$this->title, 'job_id'=>$this->id],function($u) use ($employee)
        {
            $u->from('admin@jobgrouper.com');
            $u->to($employee->email);
            $u->subject('Work begins');
        });

        //Notify for buyers that work starting
        $orders = $this->sales()->get();
        foreach ($orders as $order){
            $buyer = $order->buyer()->first();
            Mail::send('emails.job_work_start',['job_name'=>$this->title, 'job_id'=>$this->id],function($u) use ($buyer)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($buyer->email);
                $u->subject('Work begins');
            });
        }
    }


    /**
     * Start work
     */

    public function work_stop(){
        $this->status = 'waiting';
        $this->next_payment_date = 0;
        $this->make_hot();
    }

    public function endAllEarlyBirds() {
	    DB::table('early_bird_buyers')->where('job_id', $this->id)
		    ->update(['status' => 'ended']);
    }


    /**
     * Get user of job.
     */
    public function employee()
    {
        return $this->belongsTo('App\User', 'employee_id');
    }

    /**
     * Get category of job
     */
    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    /**
     * Get sales of this job
     */
    public function sales()
    {
        return $this->hasMany('App\Sale', 'job_id');
    }

    public function purchases() {
	return $this->sales()->where('status', 'in_progress')->get();
    }


    /**
     * Get buyers of this job
     *
     * - buyers with completed purchases: buyers()->where('status', 'in_progress')
     */
    public function buyers()
    {
        return $this->belongsToMany('App\User', 'sales',  'job_id', 'buyer_id');
    }

    public function confirmed_buyers() {
	return $this->buyers()->where('status', 'in_progress');
    }

    /**
     * Get job`s employee requests.
     */
    public function employee_requests()
    {
        return $this->hasMany('App\EmployeeRequest', 'job_id');
    }

    /**
     * Get job`s early_bird_buyers.
     */
    public function early_bird_buyers() {
	return $this->hasMany('App\EarlyBirdBuyer', 'job_id');
    }

    /**
     * Get job`s employee requests.
     */
    public function employee_exit_requests()
    {
        return $this->hasMany('App\EmployeeExitRequest', 'job_id');
    }

    public function buyer_adjustment_requests() {

	    return $this->hasMany('App\BuyerAdjustmentRequest', 'job_id');
    }

    /**
     * Scopes
     */
    public function scopeNotDummy($query)
    {
        return $query->where('is_dummy', false);
    }
}
