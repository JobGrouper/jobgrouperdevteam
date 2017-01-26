<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeRequest extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id', 'job_id', 'status'
    ];


    /**
     * Get the  formated_created_at value.
     *
     * @param  string  $value
     * @return string
     */
    public function getFormatedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('F j, Y g:i A');
    }

    /**
     * Get employee of request.
     */
    public function employee()
    {
        return $this->belongsTo('App\User', 'employee_id');
    }

    /**
     * Get job of request.
     */
    public function job()
    {
        return $this->belongsTo('App\Job', 'job_id');
    }
}
