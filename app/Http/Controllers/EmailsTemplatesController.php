<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use File;

class EmailsTemplatesController extends Controller
{
    public function renderEmail($emailTemplateName, $scenario=NULL){

        $originTemplatePath = '../resources/views/emails/'.$emailTemplateName.'.blade.php';
        $tmpTemplatePath = '../resources/views/emails/tmp_template.blade.php';
	
	// load json email file
	$raw_spec = file_get_contents('../resources/views/emails/email-spec.json');
	$email_spec = json_decode($raw_spec, true);
	$email_scenario = NULL;
	//var_dump($email_spec);
	//var_dump($scenario);

	if ($scenario) {

		// 1st array: $common_data 
		// 2nd array: situational_data (scenario)
		//
		$email_scenario = array_replace_recursive($email_spec[ $emailTemplateName ]['render_data'][ 'common' ],
			$email_spec[ $emailTemplateName ]['render_data'][ $scenario ]);

		return view('emails.' . $emailTemplateName, $email_scenario);
	}
	else {

		// If there is no scenario, but email has render data attached
		//
		if (isset($email_spec[ $emailTemplateName ])) {

			return view('emails.' . $emailTemplateName, $email_spec[ $emailTemplateName ]['render_data']);
		}
		// No render data has been added yet
		//
		else {
			//creating temporary template file with some changes to prevent Blade`s work
			File::copy($originTemplatePath, $tmpTemplatePath);
			$templateString = file_get_contents($tmpTemplatePath);
			$templateString = str_replace("@if", "<b>if</b>", $templateString);
			$templateString = str_replace("@endif", "<b>endif</b><br><br>", $templateString);
			$templateString = str_replace("@elseif", "<b>elseif</b><br><br>", $templateString);
			$templateString = str_replace("@else", "<b>else</b><br><br>", $templateString);
			$templateString = str_replace("{{", "<b>@{{", $templateString);
			$templateString = str_replace("}}", "}}</b>", $templateString);
			file_put_contents($tmpTemplatePath, $templateString);

			return view('emails.tmp_template');
		}
	}
    }
}
