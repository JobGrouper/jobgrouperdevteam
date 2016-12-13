<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Experience extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'experience';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'date_from', 'date_to', 'date_to_present', 'additional_info'
    ];

    /**
     * Get the  date_from valie.
     *
     * @param  string  $value
     * @return string
     */
    public function getDateFromAttribute($value)
    {
        return Carbon::parse($value)->format('m-j-Y');
    }

    /**
     * Get the  date_to valie.
     *
     * @param  string  $value
     * @return string
     */
    public function getDateToAttribute($value)
    {
        return Carbon::parse($value)->format('m-j-Y');
    }

    /**
     * Get employee of job
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
