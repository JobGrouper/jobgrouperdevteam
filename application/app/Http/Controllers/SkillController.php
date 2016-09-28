<?php

namespace App\Http\Controllers;

use App\Skill;
use Auth;
use App\User;
use Illuminate\Http\Request;


use App\Http\Requests;

class SkillController extends Controller
{
    public function store(Request $request){
        $responseData = array();

        $user = Auth::user();
        //$user = User::find(4);      //todo production

        $skill = $user->skills()->create([
            'title' => $request->title,
        ]);
        if(!$skill->id){
            $responseData['error'] = true;
            $responseData['status'] = -1;
            $responseData['info'] = 'Something went wrong. Please try again later.';
            return response($responseData, 500);
        }


        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['id'] = $skill->id;
        $responseData['info'] = 'Skill successfully created';

        return response($responseData, 200);

    }


    public function destroy(Requests\DeleteSkillRequest $request){
        $addition = Skill::find($request->id);
        if(isset($addition->id)){
            $addition->delete();
            $responseData['error'] = false;
            $responseData['status'] = 0;
            $responseData['info'] = 'Skill successfully deleted';
            return response($responseData, 200);
        }

        $responseData['error'] = true;
        $responseData['status'] = -1;
        $responseData['info'] = 'Something went wrong. Please try again later.';
        return response($responseData, 500);
    }
}
