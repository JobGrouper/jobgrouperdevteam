<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'score', 'comment', 'rated_id', 'rater_id'
    ];

    /**
     * Get rated of rate
     */
    public function buyer()
    {
        return $this->belongsTo('App\User', 'rated_id');
    }
}
