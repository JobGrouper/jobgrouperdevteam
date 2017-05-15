@extends('layouts.email')

@section('content')
<p>Your request for the "{{$job_name}}" has been approved!</p>

<p>Your request for the <a href='{{ env("SERVICE_APP_URL") }}/job/{{ $job_id }}">"{{$job_name}}'> position</a> has been approved!
You will receive a separate email if this job already has enough buyers, and is ready for you 
to begin work.</p>

<p>If this job still requires more buyers before becoming active, we'd like to invite you to 
seek out further buyers, so that you can begin work. Please share the following link with any 
potential buyers of your services: <a href='{{ env("SERVICE_APP_URL") }}/job/{{ $job_id }}'>{{ env("SERVICE_APP_URL") }}/job/{{ $job_id }}</a></p>

<p>JobGrouper will also work hard to find buyers for this position! By requiring a set 
number of buyers before our jobs become active, we try to ensure that you gain a sustainable 
income from what would normally be gig-based or part-time work with few hours. 
On the other hand, if you are satisfied with the number of buyers so far, please contact 
us at support@jobgrouper.com and we can edit the position to require fewer buyers. In 
some cases, we may be able to increase the number of buyers or adjust the salary, as well.</p>

<p>Anyhow, we're looking forward to an awesome time working together!</p>

<p>--JobGrouper</p>
@endsection