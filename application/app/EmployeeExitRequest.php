<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class EmployeeExitRequest extends Model
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
