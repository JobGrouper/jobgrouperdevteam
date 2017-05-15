@extends('layouts.main')

@section('content')
    <div class="forgotpass login">
        <div class="forgotpass_form">
            <form role="form" method="POST" action="{{ url('/password/change_submit') }}">
                {{ csrf_field() }}
		<h2>Change Password</h2>
	    @foreach ($errors->all() as $error)
		<p>** {{ $error }} **</p>
	    @endforeach
            <label for="pass">Current Password</label>
            <input type="password" id="pass" name="current_password">
            <label for="pass">New Password</label>
            <input type="password" id="pass" name="new_password">
            <label for="pass">Confirm New Password</label>
            <input type="password" id="conpass" name="confirm_new_password">
            <div class="btndiv clearfix">
                <button>Submit</button>
	    </div>
            </form>
        </div>
    </div>
@endsection
