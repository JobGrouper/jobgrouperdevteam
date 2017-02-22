@extends('layouts.email')

@section('content')
<p>You have denied the request of {{ $employee_name }} to modify the number of 
buyers on {{ $job_title }}.</p>
@endsection
