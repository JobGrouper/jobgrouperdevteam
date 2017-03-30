@extends('layouts.admin')



@section('title', 'EMAIL VIEWER')



@section('content')

    <div class="content_form">
        <div class="admintext_wrapper" style="height: 700px;">

            <select id="mailsTemplatesSelector" style="background-image: url({{asset('img/Admin/selectarrow.png')}}) !important;">
                @foreach($emails as $email)
                    <option value="{{$email}}">{{$email}}</option>
                @endforeach
            </select>

            <div id="mailTemplateContent">
                <iframe id = "mailTemplateIframe" src="/admin/renderEmailTemplate/{{$emails[0]}}" width="650px" height="500px" scrolling="yes" align="left"></iframe>
            </div>
        </div>
    </div>

@stop