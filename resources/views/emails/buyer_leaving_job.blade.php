@extends('layouts.email')

@section('content')
<p>Buyer {{ $buyer->first_name.' '.$buyer->last_name }} has leaved the job {{ $job->title }}</p>
@endsection

