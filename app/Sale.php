<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Sale extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'job_id', 'buyer_id', 'credit_card_id', 'card_set'
    ];

    /**
     * Accessor to get month_to_pay
     */
    public function getMonthToPayAttribute()
    {

        $buyer = Auth::user();
        $lastPayment = $this->payments()->orderBy('created_at', 'desc')->first();
        if($lastPayment){
            $month_to_pay = $lastPayment->month + 1;
            if($month_to_pay == 13)
                $month_to_pay = 1;

            return $month_to_pay;
        }
        else{
            return Carbon::parse($this->created_at)->format('m');
        }
    }

    /**
     * Accessor to get month_to_pay
     */
    public function getPaidToAttribute()
    {
        $buyer = Auth::user();
        $lastPayment = $this->payments()->orderBy('created_at', 'desc')->first();

        if($lastPayment){
            return Carbon::parse($this->created_at)->addMonth($this->payments()->count())->format('M d, Y');
        }
        else{
            return false;
        }
    }
    
    public function job()
    {
        return $this->belongsTo('App\Job', 'job_id');
    }

    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_id');
    }

    public function early_bird_buyer() {
	return $this->hasOne('App\EarlyBirdBuyer');
    }

    /**
     * Get orders`s close order requests
     */
    public function close_order_requests()
    {
        return $this->hasMany('App\CloseOrderRequest', 'order_id');
    }

    /**
     * Get orders`s credit card
     */
    public function credit_card()
    {
        return $this->belongsTo('App\CreditCard', 'credit_card_id');
    }

    /**
     * Get order`s payments
     */
    public function payments()
    {
        return $this->hasMany('App\Payment', 'order_id');
    }
}
