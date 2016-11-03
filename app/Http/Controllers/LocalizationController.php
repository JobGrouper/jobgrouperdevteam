<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Config;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class LocalizationController extends Controller
{
    public function SetLocalization($lang){

        if (array_key_exists($lang, Config::get('languages'))) {
            Session::set('applocale', $lang);
        }
        else{
            dd('Lang not exist');
        }

        return redirect()->back();
    }
}
