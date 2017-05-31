@extends('layouts.email')

@section('content')
<p>Your early bird access to the {{ $data['job']->title }} 
job has ended.</p>
@endsection
