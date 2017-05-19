<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EarlyBirdBuyer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'employee_id',
        'job_id',
	'sale_id',
        'status',
    ];


    /**
     * Relations
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function employee(){
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function job(){
        return $this->belongsTo(Job::class);
    }

    public function sale() {
	return $this->belongsTo(Sale::class);
    }
}
