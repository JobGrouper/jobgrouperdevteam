@extends('layouts.email')

@section('content')

@if(!{{ $changes['request_modified'] }})
<p>Your request to modify the number of buyers for {{ $job_title }} has been approved.</p>
@else
<p>Your request to modify the number of buyers for {{ $job_title }} has been approved with modifications.</p>

	@if({{ $changes['request_min_modified'] }})
	<p>The minimum number of buyers was changed to {{ $changes['new_minimum'] }}.</p>
	@endif

	@if({{ $changes['request_max_modified'] }})
	<p>The maximum number of buyers was changed to {{ $changes['new_maximum'] }}.</p>
	@endif

@endif

<p>If, upon this change, this job now has the required number of buyers to become a live job, 
you should simultaneously be receiving an email that confirms this. Your buyers will similarly 
be notified of this change and/or if this job is now live and ready to begin work.</p>
@endsection
