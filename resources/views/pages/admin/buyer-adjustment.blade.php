@extends('layouts.admin')

@section('title', 'Adjust Buyers')

@section('autoload_scripts')

<script>
	var buyer_adjuster;
	jg.Autoloader(function() {

		buyer_adjuster = new jg.BuyerAdjuster({
			root: document.getElementById('buyer_adjuster')
		});
	});
</script>
@endsection

@section('content')

	@include('partials.buyer-adjustment-form', ['requested' => false])

@stop
