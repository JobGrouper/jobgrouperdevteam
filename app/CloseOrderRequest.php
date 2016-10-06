<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CloseOrderRequest extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'originator_id', 'order_id', 'reason', 'status'
    ];

    /**
     * Get employee of request.
     */
    public function originator()
    {
        return $this->belongsTo('App\User', 'originator_id');
    }

    /**
     * Get order of request.
     */
    public function order()
    {
        return $this->belongsTo('App\Sake', 'order_id');
    }
}
