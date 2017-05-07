@extends('layouts.email')

@section('content')
<p>Looks like your payout has been updated. This usually happens when multiple payments from
your customers are combined, or when the payout is briefly delayed.</p> 

@if( $data['modified']['arrival_date'] && !$data['modified']['amount'])
<p>In this case, the arrival date has changed.</p>

@elseif( $data['modified']['amount'] && !$data['modified']['arrival_date'])
<p>In this case, the amount of the payout has changed.</p>

@elseif( $data['modified']['amount'] && $data['modified']['arrival_date'])
<p>In this case, both the amount of the payout, and the arrival date have changed.</p>

@elseif($data['modified']['other'] && !$data['modified']['arrival_date'] && !$data['modified']['amount'])
<p>In this particular case, it's not a change you need to worry about.</p>
@endif

<p>As it stands now, you can expect ${{ $data['amount'] }} to arrive in your bank account on 
{{ $data['arrival_date'] }}. We'll notify you if any further changes are made to this payout.</p>
@endsection
