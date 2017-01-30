@extends('layouts.email')

@section('content')
<p>You have successfully added {{ $job_name }} to your list of orders!</p>

@if(!$employee_exist)
    <p>You will notified when {{ $job_name }} is ready for you to confirm your order.</p>
@endif
@endsection
