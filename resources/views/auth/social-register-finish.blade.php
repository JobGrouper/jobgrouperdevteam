@extends('layouts.main')

@section('title', 'Sign Up')
@section('content')
<div class="forgotpass login signup">
    <div class="forgotpass_form">
        <form role="form" method="POST" action="{{ url('/custom_register') }}">
            {{ csrf_field() }}
            <input type="hidden" name="social_account_id" value="{{$userData['social_account_id']}}">
            <h2>Finish registration</h2>
            <div class="signup_firstlast">
                <div class="signup_firstlast__first">
                    <label for="first">First Name</label>
                    <input type="text" id="first" name="first_name" value="{{$userData['first_name']}}">
                </div>
                <div class="signup_firstlast__last">
                    <label for="last" >Last Name</label>
                    <input type="text" id="last" name="last_name" value="{{$userData['last_name']}}">
                </div>
            </div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{$userData['email']}}" readonly>
            <div class="invalid_login"></div>
            <label for="pass">Password</label>
            <input type="password" id="pass" name="password">
            <div class="radiodiv">
                <input class="radio" type="radio" id="buyer" name="user_type" value="buyer">
                <label class="buyer buy_radio" for="buyer">Buyer</label>
            </div>
            <div class="radiodiv">
                <input class="radio" type="radio" id="employee" name="user_type" value="employee">
                <label class="buyer employee_radio" for="employee">Prospective employee</label>
            </div>
            <input type="checkbox" id="terms">
            <label class="terms" for="terms">I’ve read and agree to the <a href="#">Terms &amp; Conditions</a></label>
            <div class="btndiv clearfix">
                <span><a href="#">Have an account & Log in</a></span>
                <button>Sign Up</button>
            </div>
        </form>
    </div>
</div>
@endsection
