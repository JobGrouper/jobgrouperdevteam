@extends('layouts.main')

@section('title', 'Additional Verification')

@section('content')


<section class="terms">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
		{{--<p>We haven't implemented this feature just yet. But we're going to get it working--}}
		{{--as soon as possible</p> --}}
		{{----}}
		{{--<p>Please check back with us in a couple days.</p>--}}
				<p>Additional data for Stripe verification</p>
				<form role="form" method="POST" action="{{ url('/stripe_verification_request/'.$id) }}">
					{{ csrf_field() }}
					<input name="_method" type="hidden" value="PUT">
					@foreach($fields_needed as $field_name)
						<div class="cardnumber">
							<label for="cardnumber">{{$field_name}}</label>
							<input type="text" name="{{$field_name}}">
						</div>
					@endforeach

					@if (session('message_success'))

						<span class="invalid_green">{{ session('message_success') }}</span>

					@endif

					@if (session('message_error'))

						<span class="invalid">{{ session('message_error') }}</span>
					@endif

					<button type="submit">Save</button>
				</form>
	     </div>
	</div>
    </div>
</section>
@endsection

