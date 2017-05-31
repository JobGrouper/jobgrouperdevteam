@extends('layouts.email')

@section('content')

<p>{{ $data['buyer']->full_name }} has cancelled his request for early access.</p>
@endsection
