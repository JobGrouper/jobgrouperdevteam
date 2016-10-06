<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addition extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'additions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'additional_info'
    ];

    /**
     * Get employee of job
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
