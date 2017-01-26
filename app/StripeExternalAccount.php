<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeExternalAccount extends Model
{
    public function stripeManagedAccount(){
        return $this->belongsTo('App\StripeManagedAccount', 'managed_account_id');
    }
}
