<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeManagedAccount extends Model
{
    protected $primaryKey = 'id'; // or null
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id'
    ];


    /**
     * Relations
     */
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function StripeVerificationRequests(){
        return $this->hasMany('App\StripeVerificationRequest', 'managed_account_id');
    }
}
