@extends('layouts.email')

@section('content')
<p>Your request to modify the number of buyers for {{ $job_title }} has been approved. 
If, upon this change, this job now has the required number of buyers to become a live job, 
you should simultaneously be receiving an email that confirms this. Your buyers will similarly 
be notified of this change and/or if this job is now live and ready to begin work.</p>
@endsection
