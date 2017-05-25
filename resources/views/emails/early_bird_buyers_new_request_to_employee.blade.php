@extends('layouts.email')

@section('content')
    {{$buyer->full_name}} has requested "early bird" access to your services.
    This means that, for the higher rate of ${{$job->early_bird_markup}} per month, this buyer has requested to start work now.
    You can decide if you would like to accept their request or deny it from the <a href="{{env('APP_URL').'/job/'.$job->id}}">job's page</a>.
@endsection