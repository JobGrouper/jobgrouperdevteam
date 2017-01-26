<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaintenanceWarning extends Model
{
    public $timestamps = false;
    protected $fillable = [
      'date', 'time', 'duration'
    ];
}
