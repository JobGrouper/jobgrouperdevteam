@extends('layouts.main')

@section('title', 'Sign Up')

@section('content')
<div class="forgotpass login signup">
    <div class="forgotpass_form">
        <form role="form" method="POST" action="{{ url('/custom_register') }}">
            {{ csrf_field() }}
            <h2>Sign Up</h2>
	    @foreach ($errors->all() as $error)
		<p>** {{ $error }} **</p>
	    @endforeach
            <div class="radiodiv">
                <input class="radio" type="radio" id="buyer" name="user_type" value="buyer" checked="checked" autocomplete="off">
                <label class="buyer buy_radio" for="buyer">Buyer</label>
            </div>
            <div class="radiodiv">
                <input class="radio" type="radio" id="employee" name="user_type" value="employee" autocomplete="off">
                <label class="buyer employee_radio" for="employee">Prospective employee</label>
            </div>
            <div class="login_social buyers_only">
                <a href="/social_login/facebook">
                    <span class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i>Facebook</span>
                </a>
                <a href="#">
                    <span class="twitter"><i class="fa fa-twitter"></i>Twitter</span>
                </a>
            </div>
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
            <div class="sellers_only">
                <label for="city">City</label>
                <input type="text" id="city" name="city">
                <label for="address">Address</label>
                <input type="text" id="address" name="address">
                <label for="postal">Postal code</label>
                <input type="text" id="postal" name="postal_code">
                <label for="state">State</label>

		<select name="state" autocomplete="off">
			<option value="" selected>Select a state...</option>
			<option value="AL">Alabama</option>
			<option value="AK">Alaska</option>
			<option value="AZ">Arizona</option>
			<option value="AR">Arkansas</option>
			<option value="CA">California</option>
			<option value="CO">Colorado</option>
			<option value="CT">Connecticut</option>
			<option value="DE">Delaware</option>
			<option value="DC">District Of Columbia</option>
			<option value="FL">Florida</option>
			<option value="GA">Georgia</option>
			<option value="HI">Hawaii</option>
			<option value="ID">Idaho</option>
			<option value="IL">Illinois</option>
			<option value="IN">Indiana</option>
			<option value="IA">Iowa</option>
			<option value="KS">Kansas</option>
			<option value="KY">Kentucky</option>
			<option value="LA">Louisiana</option>
			<option value="ME">Maine</option>
			<option value="MD">Maryland</option>
			<option value="MA">Massachusetts</option>
			<option value="MI">Michigan</option>
			<option value="MN">Minnesota</option>
			<option value="MS">Mississippi</option>
			<option value="MO">Missouri</option>
			<option value="MT">Montana</option>
			<option value="NE">Nebraska</option>
			<option value="NV">Nevada</option>
			<option value="NH">New Hampshire</option>
			<option value="NJ">New Jersey</option>
			<option value="NM">New Mexico</option>
			<option value="NY">New York</option>
			<option value="NC">North Carolina</option>
			<option value="ND">North Dakota</option>
			<option value="OH">Ohio</option>
			<option value="OK">Oklahoma</option>
			<option value="OR">Oregon</option>
			<option value="PA">Pennsylvania</option>
			<option value="RI">Rhode Island</option>
			<option value="SC">South Carolina</option>
			<option value="SD">South Dakota</option>
			<option value="TN">Tennessee</option>
			<option value="TX">Texas</option>
			<option value="UT">Utah</option>
			<option value="VT">Vermont</option>
			<option value="VA">Virginia</option>
			<option value="WA">Washington</option>
			<option value="WV">West Virginia</option>
			<option value="WI">Wisconsin</option>
			<option value="WY">Wyoming</option>
		</select>

                <label for="day">Birth day</label>
		{{ Form::selectRange('dob_day', 1, 31, 'Select birth day...', ['placeholder' => 'Select birth day...', 'autocomplete' => 'off']) }}
                <label for="month">Birth month</label>
		{{ Form::selectMonth('dob_month', 'Select birth month...', ['placeholder' => 'Select birth month...', 'autocomplete' => 'off']) }}
                <label for="year">Birth year</label>
		{{ Form::selectRange('dob_year', 1930, 2016, 'Select birth year...', ['placeholder' => 'Select birth year...', 'autocomplete' => 'off']) }}
                <label for="ssn">Social Security Number (last four digits only)</label>
                <input type="password" id="ssn" name="ssn_last_4" maxlength="4">
            </div>
            <input type="checkbox" id="terms">
            <label class="terms" for="terms">I’ve read and agree to our <a href="/terms">Terms &amp; Conditions</a></label>
	    <input type="checkbox" id="terms_stripe">
            <label class="terms sellers_only" for="terms">And Stripe's <a href="https://stripe.com/us/connect-account/legal">Terms &amp; Conditions</a></label>
            <div class="btndiv clearfix">
                <span>Have an account? <a href="/login">Log in</a></span>
                <button>Sign Up</button>
            </div>
        </form>
    </div>
</div>
@endsection
