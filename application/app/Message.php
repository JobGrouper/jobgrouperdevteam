<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'recipient_id', 'message'
    ];

    /**
     * Get recipient of message
     */
    public function recipient()
    {
        return $this->belongsTo('App\User', 'recipient_id');
    }
}
