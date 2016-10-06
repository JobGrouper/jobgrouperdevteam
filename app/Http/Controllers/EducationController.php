<?php

namespace App\Http\Controllers;

use App\Education;
use App\User;
use Auth;
use Illuminate\Http\Request;


use App\Http\Requests;

class EducationController extends Controller
{
    public function store(Request $request){
        $responseData = array();

        $this->validate($request, [
            'title' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);


        $user = Auth::user();
        //$user = User::find(4);          //todo production

        $education = $user->Education()->create([
            'title' => $request->title,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'additional_info' => $request->additional_info,
        ]);

        if(!$education->id){
            $responseData['error'] = true;
            $responseData['status'] = -1;
            $responseData['info'] = 'Something went wrong. Please try again later.';
            return response($responseData, 500);
        }


        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['id'] = $education->id;
        $responseData['info'] = 'Education successfully created';

        return response($responseData, 200);

    }

    public function update(Requests\EditEducationRequest $request){
        $responseData = array();
        $responseCode = 500;

        $education = Education::find($request->id);


        //todo check is user owner

        $education->fill($request->all());
        $education->save();


        $responseCode = 200;
        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['info'] = 'Education successfully updated';

        return response($responseData, $responseCode);
    }

    public function destroy(Requests\DeleteEducationRequest $request){
        $education = Education::find($request->id);
        if(isset($education->id)){
            $education->delete();
            $responseData['error'] = false;
            $responseData['status'] = 0;
            $responseData['info'] = 'Education successfully deleted';
            return response($responseData, 200);
        }

        $responseData['error'] = true;
        $responseData['status'] = -1;
        $responseData['info'] = 'Something went wrong. Please try again later.';
        return response($responseData, 500);
    }
}
