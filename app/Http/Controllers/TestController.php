<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Mail;

class TestController extends Controller
{
    public function test(){
        Mail::send('emails.confirm',['token'=>'asdasdasdasd'],function($u)
        {
            $u->from('admin@jobgrouper.com');
            $u->to('ovch2009@ukr.net');
            $u->subject('Confirm registration');
        });
    }
}
