@extends('layouts.email')

@section('content')
<p>There has been a change to your payout.</p>
<p>You can expect ${{ $data['amount'] }} to arrive in your bank account on 
{{ $data['arrival_date'] }}.</p>
@endsection
