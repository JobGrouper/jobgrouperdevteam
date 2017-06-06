@extends('layouts.main')

@section('content')
    <div class="forgotpass login">
        <div class="forgotpass_form">
            <form role="form" method="POST" action="{{ url('/auth/login') }}">
                {{ csrf_field() }}
                <h2>Log In</h2>
		<p class="red">We're currently working on Facebook log in. If you registered with us via Facebook, please create
		a new account via email.</p>
                <a href="/register" style="display: block; text-align: center; margin-bottom: 20px;">or register</a>
                <div class="login_social">
                    <!--<a href="/social_login/facebook">-->
		    <a>
                        <span class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i>Facebook</span>
                    </a>
                    <a href="/social_login/twitter">
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
