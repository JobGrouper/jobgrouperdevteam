@extends('layouts.email')

@section('content')
<p>{{ $employee_name }} has applied for the ({{ $job_name }}) position.
Please review his or her profile <a href='{{ env("SERVICE_APP_URL") }}/account/{{$id}}'>here</a> . You may approve or deny the applicant at any time.
@endsection
