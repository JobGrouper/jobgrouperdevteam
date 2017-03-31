@extends('layouts.email')

@section('content')
<p>Your job "<b>@{{$job_name}}</b>" has garnered the necessary amount of buyers and has been assigned an employee. Please login
<a href="http://jobgrouper.com/job/<b>@{{$job_id}}</b>">here</a> to communicate and begin working together</p>
@endsection