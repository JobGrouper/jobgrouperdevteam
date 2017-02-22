@extends('layouts.email')

@section('content')
<p>Our admins have decided to alter the number of buyers on {{ $job_title }}. Please check
[Job url] for the most accurate information.</p>

(if max increased) The maximum number of buyers on [Job Title] has increased to [new_maximum]. This means that you will have more responsibility to handle, but also that you'll have higher income potential.

(if max decreased) The maximum number of buyers on [Job Title] has decreased to [new_maximum], which means that the earning potential for this job is lowered, but hopefully you won't have more work than you desire.

(if min increased) The minimum number of buyers on [Job Title] has increased to [new_minimum], which means that you'll have more time to prepare yourself before work starts.

(if min decreased) The minimum number of buyers on [Job Title] has decreased to [new_minimum], which means that you'll have less time to wait until you get to start working!

If you'd like us to make any further adjusments, please visit this job's page (link) and make a request. Our admins will be happy to review any changes you suggest.
@endsection
