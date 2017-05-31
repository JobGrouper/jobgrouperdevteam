@extends('layouts.email')

@section('content')
<p>You have successfully confirmed your order for {{ $job->title }}.</p>

@if($job->status == 'working')
<p>Since this job is active, you can expect your first payment to be charged in a day or so.
After that, payments will proceed monthly. If you wish to cancel your order at any time,
visit your account's <a href='{{ env("SERVICE_APP_URL") }}/my_orders'>My Orders</a> page</p>
@else
<p>Work will begin once the minimum number of buyers has been reached. We'll be sure to let you know
when that happens. Until then, thanks for using JobGrouper!
</p>

<i>On the other hand, if you'd like to get started sooner rather than later, you can navigate to your <a href='{{ env("SERVICE_APP_URL") }}/my_orders'>my orders</a> 
page and click "buy early" for this job. The early-bird-buyer process will allow you to start work immediately.</i>
@endif
@endsection
