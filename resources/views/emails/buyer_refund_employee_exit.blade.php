@extends('layouts.email')

@section('content')

<p>As you know, {{ $data['employee']->fullname }} has left the {{ $data['job']->title }} job. We also know 
that you have paid for a full month of services that can no longer be completed. Well, it's 
only fair that you get a refund to cover that.</p>

<p>We've arranged a refund of ${{ $data['refund_amount'] }} to be sent to your bank account. 
All things considered, it should be posted in a couple day's time.</p>

<p>Thanks a lot for your patience.</p>

@endsection
