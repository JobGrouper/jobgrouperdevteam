<?php

namespace App\Http\Controllers;

use App\MaintenanceWarning;
use Illuminate\Http\Request;


class MaintenanceWarningsController extends Controller
{
    public function store(Request $requests){
        $maintenanceWarning = MaintenanceWarning::create($requests->all());
        if(isset($maintenanceWarning->id)){
            $responseCode = 200;
            $responseData['error'] = false;

            return redirect()->back();
        }
    }

    public function update($id, Request $requests){
        $maintenanceWarning = MaintenanceWarning::findOrFail($id);
        $maintenanceWarning->fill($requests->all());
        $maintenanceWarning->save();

        $responseCode = 200;
        $responseData['error'] = false;

        return response($responseData, $responseCode);

    }

    public function destroy($id){
        $maintenanceWarning = MaintenanceWarning::findOrFail($id);
        $maintenanceWarning->delete();

        $responseCode = 200;
        $responseData['error'] = false;

        return response($responseData, $responseCode);
    }
}
