@extends('layouts.email')

@section('content')
    A buyer of <a href="{{env('APP_URL').'/job/'.$job->id}}">{{$job->title}}</a> has paid for "early bird" access to your services.
    This means that, for the higher rate of ${{$job->early_bird_markup}} per month, this buyer has requested to start work now.
    You may communicate with your buyer by clicking the "message" link under his name on the Job page.
    <br>
    Please note that this does not affect other buyers of your services for <a href="{{env('APP_URL').'/job/'.$job->id}}">{{$job->title}}</a>, who will need to wait until the minimum number of buyers has been reached for work to begin.
    Other buyers may, however, also purchase early bird services, and these early bird buyers will count toward the minimum number of buyers required to begin work.
@endsection