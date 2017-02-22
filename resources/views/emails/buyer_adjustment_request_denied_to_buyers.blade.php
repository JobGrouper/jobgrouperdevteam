@extends('layouts.email')

@section('content')
<p>We recently notified you that there was a chance that we would change the number of 
buyers on {{ $job_title }}. We have determined that no changes should be made at this time. 
Everything's been going along smoothly, after all. Please stay tuned for future emails 
regarding the status of this job. If you have any questions, feel free to email 
us at support@jobgrouper.com.</p>
@endsection
