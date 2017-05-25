@extends('layouts.email')

@section('content')
    You have denied {{$buyer->full_name}}'s request to have "early bird" access to your services.
@endsection