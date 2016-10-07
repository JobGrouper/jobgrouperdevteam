@extends('layouts.admin')



@section('title', 'TEXTS')



@section('content')

    <div class="content_form">

        <div class="admintext_wrapper">

            @foreach($texts as $text)

            <div class="admintext_wrapper__item clearfix" data-id="{{$text->id}}">

                <div class="lang">{{$text->id}} EN</div>

                <p>{{$text->value}}</p>

                <input type="text" name="edit">

                <button class="save">Save</button>

                <button class="cancel">Cancel</button>

                <button class="edit"><img src="{{asset('img/Admin/textedit.png')}}" alt="alt"></button>

            </div>

            @endforeach

        </div>

    </div>

@stop