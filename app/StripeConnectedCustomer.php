<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeConnectedCustomer extends Model
{
    protected $fillable = [
        'root_customer_id',
        'user_id',
        'managed_account_id',
        'job_id',
    ];

    /**
     * Relationns
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function job(){
        return $this->belongsTo(Job::class);
    }

    public function managed_account(){
        return $this->belongsTo(StripeManagedAccount::class);
    }
}
