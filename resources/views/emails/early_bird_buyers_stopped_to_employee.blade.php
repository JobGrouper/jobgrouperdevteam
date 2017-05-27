@extends('layouts.email')

@section('content')
<p>{{ $data['buyer']->full_name }}'s early bird access to the {{ $data['job']->title }} 
job has ended.</p>
@endsection
