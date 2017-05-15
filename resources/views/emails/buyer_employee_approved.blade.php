@extends('layouts.email')

@section('content')
<p>We have approved {{ $employee_name }} to work
on {{ $job_name }}. The job will officially begin once all buyers have placed an 
order and set their payment information.</p>

<p>In order to make sure this process moves as swiftly as possible, we ask that 
you submit your credit/debit card information as soon as you can, following 
<a href='{{ env("SERVICE_APP_URL") }}/purchase/{{ $order_id }}'>this link directly</a>, or from your
<a href='{{ env("SERVICE_APP_URL") }}/my_orders'>My Orders</a> page.</p>

<p>You're helping build the future of employment, workers are gaining a steady 
paycheck and you're getting the job done at a great price. Thanks for putting your faith in us!</p>
@endsection
