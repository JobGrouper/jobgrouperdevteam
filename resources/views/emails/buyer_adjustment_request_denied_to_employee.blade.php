@extends('layouts.email')

@section('content')
<p>Your buyer adjustment request for {{ $job_title }} has been denied. If you would like to make a new 
request, please feel free to submit one at any time.</p>

<p>Please do not re-submit the same request twice, however, since it will likely be 
rejected again. If you feel that your previous request should not have been denied, 
and you have a compelling reason to believe so, please email us at support@jobgrouper.com.</p>
@endsection
