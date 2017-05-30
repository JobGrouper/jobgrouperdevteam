<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeSubscription extends Model
{
    protected $fillable = [
        'plan_id',
        'managed_account_id',
        'activated',
    ];

    /**
     * Relationns
     */
    public function connected_customer(){
        return $this->belongsTo(StripeConnectedCustomer::class);
    }

    public function plan(){
        return $this->belongsTo(StripePlan::class);
    }
}
