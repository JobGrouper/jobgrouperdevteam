<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyerAdjustment extends Model
{
    protected $fillable = [
      'job_id',
      'employee_id',
      'from_request_id',
      'old_client_max',
      'old_client_min',
      'new_client_max',
      'new_client_min',
    ];
}
