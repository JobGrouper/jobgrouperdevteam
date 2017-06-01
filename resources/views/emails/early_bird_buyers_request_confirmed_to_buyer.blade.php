@extends('layouts.email')

@section('content')
<p>{{ $employee->full_name }} has accepted your request for "early bird" access to their services 
at the increased rate of ${{ number_format($job->early_bird_markup, 2) }} per month. You can communicate with 
{{ $employee->full_name }} through your <a href="{{ env('SERVICE_APP_URL'). '/my_orders' }}">My Orders</a> page.</p>

<p>Other buyers may also purchase early bird services, when that happens, the increased rate that 
you pay will decrease and these early bird buyers will count toward the minimum number of buyers 
required to begin work officially.</p>
@endsection
