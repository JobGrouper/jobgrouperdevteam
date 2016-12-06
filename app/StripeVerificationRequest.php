<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeVerificationRequest extends Model
{
    protected $fillable = [
        'managed_account_id', 'fields_needed', 'completed'
    ];
}
