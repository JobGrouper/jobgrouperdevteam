@extends('layouts.email')

@section('content')
<p>Your job "{{$job_name}}" has garnered the necessary amount of buyers and has been assigned an employee. Please login
<a href="http://jobgrouper.com/job/{{$job_id}}">here</a> to communicate and begin working together</p>
@endsection