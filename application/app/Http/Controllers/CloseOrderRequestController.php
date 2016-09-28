<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\CloseOrderRequest;

use Illuminate\Support\Facades\Auth;

use App\User;
class CloseOrderRequestController extends Controller
{
    public function store(Request $request){

        $user = Auth::user();
        //$user = User::find(5);          //todo production
        $responseData = array();


        //Check if user has not already make request to close this order
        if (CloseOrderRequest::where('originator_id', '=', $user->id)->where('order_id', '=', $request->order_id)->count() > 0) {
            $responseData['error'] = true;
            $responseData['status'] = 1;
            $responseData['info'] = 'You already have done request to close this order';
            return response($responseData, 200);
        }


        $this->validate($request, [
            'order_id' => 'order_id'
        ]);



        $CloseOrderRequest = $user->close_order_requests()->create([
            'order_id' => $request->order_id,
        ]);

        if(!$CloseOrderRequest->id){
            $responseData['error'] = true;
            $responseData['status'] = -1;
            $responseData['info'] = 'Something went wrong. Please try again later.';
            return response($responseData, 500);
        }


        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['info'] = 'Request successfully created';

        return response($responseData, 200);

    }
}
