<?php

namespace App\Http\Controllers;

use App\PageText;
use Illuminate\Http\Request;

use App\Http\Requests;

class PageTextsController extends Controller
{
    public function update(Request $request){
        $responseData = array();
        $text = PageText::find($request->id);

        //todo check is user owner

        $text->fill($request->all());
        $text->save();


        $responseCode = 200;
        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['info'] = 'Text successfully updated';

        return response($responseData, $responseCode);
    }
}
