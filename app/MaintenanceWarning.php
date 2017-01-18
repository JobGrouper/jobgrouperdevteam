<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaintenanceWarning extends Model
{
    protected $fillable = [
      'text', 'date_from', 'date_to'
    ];
}
