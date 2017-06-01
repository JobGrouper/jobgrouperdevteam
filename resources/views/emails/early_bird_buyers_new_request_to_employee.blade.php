@extends('layouts.email')

@section('content')
<p>{{$user->full_name}} has requested "early bird" access to your services.
This means that, for the higher rate of ${{ number_format( $job->next_markup, 2) }} per month, 
this buyer has requested to start work now.</p>

<p>You can decide if you would like to accept their request or deny it from 
the <a href="{{env('SERVICE_APP_URL').'/job/'.$job->id}}">job's page</a></p>.
@endsection
