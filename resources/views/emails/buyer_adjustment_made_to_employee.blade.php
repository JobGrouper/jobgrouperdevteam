@extends('layouts.email')

@section('content')
<p>Our admins have decided to alter the number of buyers on {{ $job_title }}. Please check
<a href="{{ env("SERVICE_APP_URL") }}/job/{{ $job_id }}">the job page</a> for the most accurate information.</p>

@if( $changes['max_change'] == 'increase' ) 
<p>The maximum number of buyers on {{ $job_title }} has increased to {{ $changes['new_maximum'] }}. 
This means that you will have more responsibility to handle, 
but also that you'll have higher income potential.
<p>
@endif

@if($changes['max_change'] == 'decrease') 
<p>The maximum number of buyers on {{ $job_title }} has decreased to {{ $changes['new_maximum'] }}, which means 
that the earning potential for this job is lowered, but hopefully you won't have more work than you desire.</p>
@endif

@if($changes['min_change'] == 'increase') 
<p>The minimum number of buyers on {{ $job_title }} has increased to {{ $changes['new_minimum'] }}, 
which means that you'll have more time to prepare yourself before work starts.</p>
@endif

@if($changes['min_change'] == 'decrease') 
<p>The minimum number of buyers on {{ $job_title }} has decreased to {{ $changes['new_minimum'] }}, 
which means that you'll have less time to wait until you get to start working!</p>
@endif

<p>If you'd like us to make any further adjusments, 
please visit this <a href="{{ env("SERVICE_APP_URL") }}/job/{{ $job_id }}">this job's page</a> and 
make a request. Our admins will be happy to review any changes you suggest.</p>
@endsection
