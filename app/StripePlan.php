<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripePlan extends Model
{
    protected $fillable = [
        'managed_account_id',
        'job_id',
        'activated',
    ];

    /**
     * Relationns
     */
    public function connected_customer(){
        return $this->belongsTo(StripeConnectedCustomer::class);
    }

    public function managed_account(){
        return $this->belongsTo(StripeManagedAccount::class);
    }
}
