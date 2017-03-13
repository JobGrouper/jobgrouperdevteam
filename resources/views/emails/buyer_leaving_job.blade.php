@extends('layouts.email')

@section('content')
<p>Buyer {{ $buyer->first_name.' '.$buyer->last_name }} has left the job {{ $job->title }}.</p>
@endsection

