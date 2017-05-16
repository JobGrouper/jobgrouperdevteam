@extends('layouts.email')

@section('content')
<p>Due to your imminent exit, your buyers are being refunded
for the time that you have spent working this month.</p>

<p>A refund has just been sent out to {{ $data['buyer']->full_name }} in the amount 
of ${{ $data['refund_amount'] }}.</p>
@endsection
