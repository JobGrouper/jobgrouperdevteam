<?php

namespace App\Http\Controllers;

use App\Addition;
use Auth;
use Illuminate\Http\Request;


use App\Http\Requests;

class AdditionController extends Controller
{
    public function store(Request $request){
        $responseData = array();

        $user = Auth::user();
        //$user = User::find(4);      //todo production

        $addition = $user->additions()->create([
            'title' => $request->title,
            'additional_info' => $request->additional_info,
        ]);



        if(!$addition->id){
            $responseData['error'] = true;
            $responseData['status'] = -1;
            $responseData['info'] = 'Something went wrong. Please try again later.';
            return response($responseData, 500);
        }


        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['id'] = $addition->id;
        $responseData['info'] = 'Addition successfully created';

        return response($responseData, 200);

    }

    public function update(Requests\EditAdditionRequest $request){
        $responseData = array();
        $responseCode = 500;

        $experience = Addition::find($request->id);

        //todo check is user owner

        $experience->fill($request->all());
        $experience->save();


        $responseCode = 200;
        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['info'] = 'Addition successfully updated';

        return response($responseData, $responseCode);
    }

    public function destroy(Requests\DeleteAdditionRequest $request){
        $addition = Addition::find($request->id);
        if(isset($addition->id)){
            $addition->delete();
            $responseData['error'] = false;
            $responseData['status'] = 0;
            $responseData['info'] = 'Addition successfully deleted';
            return response($responseData, 200);
        }

        $responseData['error'] = true;
        $responseData['status'] = -1;
        $responseData['info'] = 'Something went wrong. Please try again later.';
        return response($responseData, 500);
    }
}
