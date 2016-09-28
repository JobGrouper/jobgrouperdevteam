<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>@yield('title')</title>

    <link rel="icon" type="image/ico" href="{{ asset('img/jb_16.ico') }}" sizes="16x16">
    <link rel="icon" type="image/ico" href="{{ asset('img/jb_32.ico') }}" sizes="32x32">
    <link rel="icon" type="image/ico" href="{{ asset('img/jb_64.ico') }}" sizes="64x64">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,800italic,800,700italic,700,600italic,400italic,600,300,300italic' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="{{ asset('libs/bootstrap/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/font-awesome-4.2.0/css/font-awesome.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('libs/magnific/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/pickmeup/pickmeup.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/Cropper/assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/Cropper/dist/cropper.css') }}">

    <link rel="stylesheet" href="{{ asset('css/libs.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    <link rel="stylesheet" href="{{ asset('css/media.css') }}">

</head>

<body>


@include('partials.small-header')





@yield('content')





@include('partials.footer')



</body>

</html>