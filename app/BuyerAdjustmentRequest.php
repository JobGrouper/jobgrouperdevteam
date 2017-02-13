<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyerAdjustmentRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id', 'employee_id', 'current_client_max', 'current_client_min', 'requested_client_min', 'requested_client_max', 'accepted'
    ];
}
