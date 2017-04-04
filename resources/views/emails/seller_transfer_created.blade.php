@extends('layouts.email')

@section('content')
<p>A payment from one of your buyers, in the amount of {{ data.amount }}, is on its way! 
We expect it to be available within 7 days (if this is your first payment on JobGrouper), 
or 3 days for all subsequent payments. Please be in touch with us at support@jobgrouper.com 
if you do not receive your funds for any reason. We will be sending further emails as 
the process continues.</p>
@endsection
