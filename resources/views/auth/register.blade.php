@extends('layouts.main')

@section('title', 'Sign Up')

@section('content')
<div class="forgotpass login signup">
    <div class="forgotpass_form">
        <form role="form" method="POST" action="{{ url('/custom_register') }}">
            {{ csrf_field() }}
            <h2>Sign Up</h2>
            <div class="login_social">
                <a href="/social_login/facebook">
                    <span class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i>Facebook</span>
                </a>
                <a href="#">
                    <span class="twitter"><i class="fa fa-twitter"></i>Twitter</span>
                </a>
                
            </div>
            <label for="city">City</label>
            <input type="text" id="city" name="city">
            <label for="address">Address</label>
            <input type="text" id="address" name="address">
            <label for="postal">Postal code</label>
            <input type="text" id="postal" name="postal">
            <label for="state">State</label>
            <input type="text" id="state" name="state">
            <label for="day">Birth day</label>
            <input type="text" id="dat" name="day">
            <label for="month">Birth month</label>
            <input type="text" id="month" name="month">
            <label for="year">Birth year</label>
            <input type="text" id="year" name="year">
            <label for="card">Debit Card information</label>
            <input type="text" id="card" name="card">
            <label for="ssn">SSN</label>
            <input type="password" id="ssn" name="ssn">
            <div class="signup_firstlast">
                <div class="signup_firstlast__first">
                    <label for="first">First Name</label>
                    <input type="text" id="first" name="first_name">
                </div>
                <div class="signup_firstlast__last">
                    <label for="last" >Last Name</label>
                    <input type="text" id="last" name="last_name">
                </div>
            </div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
            <div class="invalid_login"></div>
            <label for="pass">Password</label>
            <input type="password" id="pass" name="password">
            <label for="pass">Confirm Password</label>
            <input type="password" id="conpass" name="confirm_password">
            <div class="radiodiv">
                <input class="radio" type="radio" id="buyer" name="user_type" value="buyer">
                <label class="buyer buy_radio" for="buyer">Buyer</label>
            </div>
            <div class="radiodiv">
                <input class="radio" type="radio" id="employee" name="user_type" value="employee">
                <label class="buyer employee_radio" for="employee">Prospective employee</label>
            </div>
            <input type="checkbox" id="terms">
            <label class="terms" for="terms">I’ve read and agree to the <a href="/terms">Terms &amp; Conditions</a></label>
            <div class="btndiv clearfix">
                <span>Have an account? <a href="/login">Log in</a></span>
                <button>Sign Up</button>
            </div>
        </form>
    </div>
</div>
@endsection
