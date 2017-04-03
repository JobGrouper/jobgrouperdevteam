@extends('layouts.email')

@section('content')
<style>
    a{
        transition: all .3s ease;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -o-transition: all .3s ease;
        display: block;
        width: 150px;
        text-align: center;
        margin: 20px auto;
        padding: 15px 20px;
        color: #fff;
        background: #3e6372;
        text-decoration: none;
        border-radius: 5px;
    }
</style>

<h1>Confirm Your Email</h1>
<p>Thanks for creating an account on JobGrouper!<br>Please confirm your email to verify your account by clicking on the button below</p>
<a href='{{ env("SERVICE_APP_URL") }}/register/confirm/{{$token}}'>Verify email</a>
@endsection
