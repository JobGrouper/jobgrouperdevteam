@extends('layouts.email')

@section('content')

<p>First and foremost, we want you to know that your individual monthly payment has not 
(and will not) change because of any changes to the number of buyers. This courtesy 
notice is meant to let you know how a change in the number of buyers might affect work 
on this job.<p>

@if($changes['max_change'])
@if($changes['max_change'] == 'increase')
<p>The maximum number of buyers on {{ $job_title }} has increased to {{ $changes['new_maximum'] }}. This 
means that {{ $employee_name }} may be balancing more responsibility. However, 
we want to assure you that we wouldn't have approved this change if we didn't think 
they could handle it. We promise that the quality of {{ $employee_first_name }}'s work 
will not suffer. If you notice anything to the contrary, please do not hesitate 
to contact us at support@jobgrouper.com.</p>
@endif
@if($changes['max_change'] == 'decrease')
<p>The maximum number of buyers on {{ $job_title }} has decreased to {{ $changes['new_maximum'] }}. We 
believe that this change will work to everybody's benefit. However, if you have any problems 
with this new arrangement, please do not hesitate to contact us at support@jobgrouper.com.</p>
@endif
@endif

@if($changes['min_change'])
@if($changes['min_change'] == 'increase')
<p>The minimum number of buyers on {{ $job_title }} has increased to {{ $changes['new_minimum'] }}, 
which means that there will be a longer wait until the job begins and you make your 
first payment. Please stay tuned for future emails regarding the status of this job.</p>
@endif
@if($changes['min_change'] == 'decrease')
<p>The minimum number of buyers on {{ $job_title }} has decreased to {{ $changes['new_minimum'] }}, 
which means that you'll have less time to wait until you get to start working! Since this 
job may become active sooner that you might have expected, please stay tuned for future 
emails regarding the status of this job.</p>
@endif
@endif
@endsection
