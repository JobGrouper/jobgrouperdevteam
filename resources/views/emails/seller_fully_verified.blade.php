@extends('layouts.email')

@section('content')

<h1>You're Fully Verified!</h1>
<p>Congratulations! You're fully verified and all set to begin working.
Don't forget to add your education and experience to your <a href="{{ env('SERVICE_APP_URL') }}/account">account
page</a>, so that you can present your best self to our hiring team,
and to individual buyers who will see your profile.</p>

<p>Please check JobGrouper's homepage for all the latest jobs. You may
apply to as many jobs as you desire, or focus on the one the most
closely fits your skill set. If you're not chosen for one particular
job, please come back and apply for more.</p>

<p>If you're chosen for a job on our site, and it receives the required
amount of buyers, you will be able to begin work and start receiving
your hard-earned pay!</p>

<p>We wish you the best of luck!</p>

<p>--JobGrouper</p>
@endsection
