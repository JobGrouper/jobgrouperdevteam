<?php
if(Request::input('fromJob')){
    Session::put('formJob', Request::input('fromJob'));
    //Session::forget('formJob');
}
?>

@extends('layouts.main')

@section('content')
    <div class="forgotpass login">
        <div class="forgotpass_form">
            <form role="form" method="POST" action="{{ url('/auth/login') }}">
                {{ csrf_field() }}
                <h2>Log In</h2>
                <a href="/register" style="display: block; text-align: center; margin-bottom: 20px;">or register</a>
                <div class="login_social">
                    <a href="/social_login/facebook">
                        <span class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i>Facebook</span>
                    </a>
                    <a onclick="alert('Sorry, we\'re still working on Twitter login')">
                        <span class="twitter"><i class="fa fa-twitter"></i>Twitter</span>
                    </a>
                </div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
                <label for="pass">Password</label>
                <input type="password" id="pass" name="password">
                 <div class="invalid_login">
                    @if(Session::has('message'))
                        {{Session::get('message')}}
                    @endif
                </div>
                <input type="checkbox" id="remember" name="remember">
                <label class="label_remember" for="remember">remember me</label>
                <a href="/password/email" class="forgot">forgot password?</a>
                <button class="login_enter">Log In</button>
            </form>
        </div>
    </div>
@endsection
