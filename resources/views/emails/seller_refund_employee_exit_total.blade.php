@extends('layouts.email')

@section('content')
<p>Due to your imminent exit, your buyers are being refunded
for the time that you have spent working this month. </p>

<p>In total, ${{ $data['total_refund'] }} is being refunded to your clients</p>
@endsection
