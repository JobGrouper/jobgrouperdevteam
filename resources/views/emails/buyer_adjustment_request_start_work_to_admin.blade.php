@extends('layouts.email')

@section('content')

<p>{{ $employee_name }}, the employee attached to {{ $job_title }} has requested to start work immediately</p>

<p>Please access the admin panel to accept, reject, or modify this request.</p>
@endsection
