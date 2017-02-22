@extends('layouts.email')

@section('content')

<p>{{ $employee_name }}, the employee attached to {{ $job_title }} has requested the following change(s):</p>

<ul>
@if($changes['max_change'])
<li>That the maximum number of buyers for this job be changed from [current #buyers required] to [new #buyers required].</li>
@endif
@if($changes['min_change'])
<li>That the minimum number of buyers for this job be changed from [current #buyers required] to [new #buyers required].</li>
@endif
</ul>

<p>Please access the admin panel to accept, reject, or modify this request.</p>
@endsection
