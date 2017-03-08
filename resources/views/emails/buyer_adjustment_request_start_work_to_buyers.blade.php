@extends('layouts.email')

@section('content')
<p>{{ $employee_name }}, the employee attached to {{ $job_title }}, has sent a request to begin work immediately.<p>

<p>We want you to know that this will not change your individual monthly payment. This 
courtesy notice is only meant to let you know that work may begin sooner than expected.</p>

<p>You will be notified of any changes to this job as soon as they have 
been approved by JobGrouper admins.</p>
@endsection
