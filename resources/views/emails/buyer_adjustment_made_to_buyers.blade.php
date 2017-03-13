@extends('layouts.email')

@section('content')
<p>Our admins have decided to alter the number of buyers on {{ $job_title }}. Please check
<a href="{{ env("SERVICE_APP_URL") }}/job/{{ $job_id }}">the job page</a> for the most accurate information.</p>

<p>Your individual monthly payment has not 
(and will not) change because of any changes to the number of buyers. This courtesy 
notice is meant to let you know how a change in the number of buyers might affect work 
on this job.</p>

@if($changes['max_change'])
@if($changes['max_change'] == 'increase')
<p>The maximum number of buyers on {{ $job_title }} has increased to {{ $changes['new_maximum'] }}.</p>
@endif
@if($changes['max_change'] == 'decrease')
<p>The maximum number of buyers on {{ $job_title }} has decreased to {{ $changes['new_maximum'] }}.</p>
@endif
@endif

@if($changes['min_change'])
@if($changes['min_change'] == 'increase')
<p>The minimum number of buyers on {{ $job_title }} has increased to {{ $changes['new_minimum'] }}, 
which means that there will be a longer wait until the job begins and you make your 
first payment.</p>
@endif
@if($changes['min_change'] == 'decrease')
<p>The minimum number of buyers on {{ $job_title }} has decreased to {{ $changes['new_minimum'] }}, 
which means that you'll have less time to wait until you get to start working!</p>
@endif
@endif

<p>We expect things to proceed smoothly, however, if you have any problems 
with this new arrangement, please do not hesitate to contact us at support@jobgrouper.com.</p>

@endsection
