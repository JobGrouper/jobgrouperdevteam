<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_id', 'card_id','valid_until','type','number','expire_month', 'expire_year', 'first_name', 'last_name'
    ];


    /**
     * Get owner of credit card
     */
    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }
}
