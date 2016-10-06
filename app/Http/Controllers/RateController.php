<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;

class RateController extends Controller
{
    public function store(Request $request){
        $responseData = array();
        $user = Auth::user();

        $ratedUser = User::findOrFail($request->rated_id);

        $rate = $ratedUser->rates()->create([
            'score' => $request->score,
            'comment' => $request->comment,
            'rater_id' => $user->id,
        ]);

        if(isset($rate->id)){
            $responseData['error'] = false;
            return response($responseData, 200);
        }
        else{
            $responseData['error'] = true;
            return response($responseData, 200);
        }
    }
}
