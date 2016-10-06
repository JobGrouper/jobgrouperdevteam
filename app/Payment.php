<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'buyer_id', 'order_id', 'amount', 'payment_system', 'status', 'month'
    ];


    /**
     * Get buyer of payment
     */
    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_id');
    }

    /**
     * Get order of payment
     */
    public function order()
    {
        return $this->belongsTo('App\Sale', 'order_id');
    }

    /**
     * Get the  formated_created_at value.
     *
     * @param  string  $value
     * @return string
     */
    public function getFormatedCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('F j, Y g:i A');
    }

    /**
     * Get the  transaction_id value.
     *
     * @param  string  $value
     * @return string
     */
    public function getTransactionIdAttribute()
    {
        return Carbon::parse($this->created_at)->format('my').$this->id;
    }
}
