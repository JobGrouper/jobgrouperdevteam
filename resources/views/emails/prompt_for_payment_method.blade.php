@extends('layouts.email')

@section('content')
<h1>Please set a payment method</h1>

<p>We noticed that you haven't set up a payment method yet. 
Setting this up will help us finish verifying your account, 
and having a verified account will ensure that you have the 
smoothest possible experience with JobGrouper.</p>

<p>It's a simple process. All you have to do is go to 
your <a href="{{ env("SERVICE_APP_URL") }}/account">account page</a>, find the Payment Options section 
and fill in the form provided. After that, we'll take care 
of the rest. It's what we do.</p>

<p>Thanks for working with us!</p>

<p>--JobGrouper</p>
@endsection
