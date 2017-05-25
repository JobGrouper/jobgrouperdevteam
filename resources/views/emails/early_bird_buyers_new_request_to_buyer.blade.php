@extends('layouts.email')

@section('content')
    Your request for early bird access to {{$job->title}} has been sent to {{$employee->full_name}}. You will be notified as soon as they accept or deny your request.
@endsection
