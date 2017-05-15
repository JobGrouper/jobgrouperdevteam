@extends('layouts.email')

@section('content')
<h1>We Need Some More Information From You</h1>

<p>It seems that we will require more information from you in order to fully verify your account.
Not to worry, it shouldn't take long.</p>

<p>You can follow this <a href='{{ env("SERVICE_APP_URL") }}/account/additional_info/{{ $request_id }}'>link</a> to a form that
will prompt you for the information that we need to get you fully verified. Once that information is sent off, we'll keep you informed
on how the process is going. Typically, the verification process is completed in a few hours.</p>

<p>In the meantime, you can still apply for jobs and begin working, 
however, there will be a temporary limit to the amount of funds you can receive.
Don't worry, though. The verification process should be resolved long before this 
becomes a problem.</p>

<p>Thanks for working with us!</p>

<p>--JobGrouper</p>
@endsection
