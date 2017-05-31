@extends('layouts.email')

@section('content')

<p>You have successfully cancelled your request for early access to {{ $data['job']->title }}.</p>
@endsection
