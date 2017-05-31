@extends('layouts.email')

@section('content')
<p>You have denied {{$buyer->full_name}}'s request to have "early bird" access to your services.</p>
@endsection
