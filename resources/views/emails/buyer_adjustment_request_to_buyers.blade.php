@extends('layouts.email')

@section('content')
<p>{{ $employee_name }}, the employee attached to [Job Name] has requested the following change(s):<p>

@if($changes['max_change'])
<p>- That the maximum number of buyers for this job be changed from [current #buyers required] to [new #buyers required].</p>
@endif

@if($changes['min_change'])
<p>- That the minimum number of buyers for this job be changed from [current #buyers required] to [new #buyers required].</p>

@if($changes['min_change'] == 'decrease')
<p>If approved, this will mean that work will begin sooner than expected.</p>
@endif

@if($changes['min_change'] == 'increase')
<p>If approved, this will mean that work will begin later than expected.</p>
@endif
@endif

<p>Most importantly, though, we want you to know that your individual monthly payment has 
not (and will not) change because of any changes to the number of buyers. This 
courtesy notice is meant to let you know how a change in the number of buyers might 
affect work on this job.</p>

<p>You will be notified of any changes to this job as soon as they have 
been approved by JobGrouper admins.</p>
@endsection
