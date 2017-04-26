@extends('layouts.email')

@section('content')
<p>Looks like your payout has been updated. This usually happens when multiple payments from
your customers are combined, or when the payout is briefly delayed.</p> 

@if( $data['modified_count'] == 1 || ( $data['modified_count'] == 2 && $data['modified']['other']))

	@if( $data['modified']['arrival_date'] )
	<p>In your case, the arrival date has changed</p>
	@endif

	@if( $data['modified']['amount'] )
	<p>In your case, the amount of the payout has changed.</p>
	@endif

	@if( $data['modified']['other'] )
	<p>In your case, it's not a change you need to worry about.</p>
	@endif

@else
<p>In your case, the following has changed:</p>
<ul>
	@if( $data['modified']['arrival_date'] )
	<li>The arrival date</li>
	@endif
	@if( $data['modified']['amount'] )
	<li>The amount</li>
	@endif
</ul>
@endif

<p>As it stands now, you can expect ${{ $data['amount'] }} to arrive in your bank account on 
{{ $data['arrival_date'] }}. We'll notify you if any further changes are made to this payout.</p>
@endsection
