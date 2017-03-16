<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class HomepageBgImagesController extends Controller
{
    public function store(Request $request){
        $imageHash = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image_hash));
        $imagePath = config('app.homepage_bg_images_dir').$request->image_name.'.png';

        if(file_exists($imagePath)){
            return response([
                'status' => 'X',
                'data' => null,
                'message' => 'Image with this name already exist!',
            ], 200);
        }
        if(file_put_contents($imagePath, $imageHash)){
            return response([
                'status' => 'OK',
                'data' => null,
                'message' => '',
            ], 200);
        }
        else{
            return response([
                'status' => 'X',
                'data' => null,
                'message' => '',
            ], 200);
        }
    }

    public function delete(Request $request){

        $imagePath = config('app.homepage_bg_images_dir').$request->image_name.'.png';

        if(!file_exists($imagePath)){
            return response([
                'status' => 'X',
                'data' => null,
                'message' => 'Image with this name does not exist!',
            ], 200);
        }

        if(unlink(config('app.homepage_bg_images_dir').$request->image_name.'.png')){
            return response([
                'status' => 'OK',
                'data' => null,
                'message' => '',
            ], 200);
        }
        else{
            return response([
                'status' => 'X',
                'data' => null,
                'message' => '',
            ], 200);
        }
    }
}
