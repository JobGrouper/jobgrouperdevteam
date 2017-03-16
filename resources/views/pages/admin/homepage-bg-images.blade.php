@extends('layouts.admin')



@section('title', 'HOMEPAGE BACKGROUND IMAGES')



@section('content')
    <span style="margin-left: 100px;">(Form for uploading new images (using cropper) will be here..)</span>
    <div class="content_form">

        <div class="admintext_wrapper">
            @foreach($imagesArray as $image)
            <div class="admintext_wrapper__item clearfix">
                <p>{{$image}} <span style="margin-left: 100px;">(thumbnail will be here )</span></p>
                <button class="edit"><img src="{{asset('img/Admin/delete.png')}}" alt="alt"></button>
            </div>
            @endforeach
        </div>

    </div>

@stop