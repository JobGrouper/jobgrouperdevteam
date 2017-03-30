<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use File;

class EmailsTemplatesController extends Controller
{
    public function renderEmail($emailTemplateName){

        $originTemplatePath = '../resources/views/emails/'.$emailTemplateName.'.blade.php';
        $tmpTemplatePath = '../resources/views/emails/tmp_template.blade.php';

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
