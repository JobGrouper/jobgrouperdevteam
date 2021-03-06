<?php

namespace App\Http\Controllers;

use App\Experience;
use App\User;
use Auth;
use Illuminate\Http\Request;


use App\Http\Requests;

class ExperienceController extends Controller
{
    public function store(Request $request){
        $responseData = array();

        $this->validate($request, [
            'title' => 'required',
            'date_from' => 'required',
            'date_to' => 'required_if:date_to_present,false',
	    'date_to_present' => 'required_without:date_to'
        ]);


        $user = Auth::user();
	$date_from = \DateTime::createFromFormat('m-d-Y', $request->date_from )->format('Y-m-d H:i:s');

	if ($request->date_to !== '')
	  $date_to = \DateTime::createFromFormat('m-d-Y', $request->date_to )->format('Y-m-d H:i:s');
	else
	  $date_to = $request->date_to;

	if ($request->date_to_present == 'true')
	  $date_to_present = 1;
	else
	  $date_to_present = 0;

        $experience = $user->experience()->create([
            'title' => $request->title,
            'date_from' => $date_from,
            'date_to' => $date_to,
	    'date_to_present' => $date_to_present,
            'additional_info' => $request->additional_info,
        ]);

        if(!$experience->id){
            $responseData['error'] = true;
            $responseData['status'] = -1;
            $responseData['info'] = 'Something went wrong. Please try again later.';
            return response($responseData, 500);
        }


        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['id'] = $experience->id;
        $responseData['info'] = 'Experience successfully created';

        return response($responseData, 200);

    }

    public function update(Requests\EditExperienceRequest $request){
        $responseData = array();
        $responseCode = 500;

        $experience = Experience::find($request->id);

        //todo check is user owner
	//

        $experience->fill($request->all());
	$experience->date_from = \DateTime::createFromFormat('m-d-Y', $request->date_from )->format('Y-m-d H:i:s');

	if ($request->date_to !== '')
	  $experience->date_to = \DateTime::createFromFormat('m-d-Y', $request->date_to )->format('Y-m-d H:i:s');
	else
	  $experience->date_to = $request->date_to;

	if ($request->date_to_present == 'true')
	  $experience->date_to_present = 1;
	else
	  $experience->date_to_present = 0;

        $experience->save();


        $responseCode = 200;
        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['info'] = 'Experience successfully updated';

        return response($responseData, $responseCode);
    }

    public function destroy(Requests\DeleteExperienceRequest $request){
        $experience = Experience::find($request->id);
        if(isset($experience->id)){
            $experience->delete();
            $responseData['error'] = false;
            $responseData['status'] = 0;
            $responseData['info'] = 'Experience successfully deleted';
            return response($responseData, 200);
        }

        $responseData['error'] = true;
        $responseData['status'] = -1;
        $responseData['info'] = 'Something went wrong. Please try again later.';
        return response($responseData, 500);
    }
}
