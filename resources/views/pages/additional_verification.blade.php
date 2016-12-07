@extends('layouts.main')

@section('title', 'Additional Verification')

@section('content')


	<div class="forgotpass login signup">
		<div class="forgotpass_form">
				<p>Additional data for Stripe verification</p>
				@if (session('message_success'))
					<span class="invalid_green">{{ session('message_success') }}</span>
				@else
					<form role="form" method="POST" enctype="multipart/form-data" action="{{ url('/stripe_verification_request/'.$id) }}">
						{{ csrf_field() }}
						<input name="_method" type="hidden" value="PUT">
						@foreach($fields_needed as $field_name)
							@if($field_name == 'legal_entity.address.city')
								<label for="city">City</label>
								<input type="text" id="city" name="stripeAccountData[legal_entity][address][city]">
							@elseif($field_name == 'legal_entity.address.line1')
								<label for="address">Address</label>
								<input type="text" id="address" name="stripeAccountData[legal_entity][address][line1]">
							@elseif($field_name == 'legal_entity.address.postal_code')
								<label for="postal">Postal code</label>
								<input type="text" id="postal" name="stripeAccountData[legal_entity][address][postal_code]">
							@elseif($field_name == 'legal_entity.address.state')
								<label for="state">State</label>
								<select name="stripeAccountData[legal_entity][address][state]" autocomplete="off">
									<option value="" selected>Select a state...</option>
									<option value="AL">Alabama</option>
									<option value="AK">Alaska</option>
									<option value="AZ">Arizona</option>
									<option value="AR">Arkansas</option>
									<option value="CA">California</option>
									<option value="CO">Colorado</option>
									<option value="CT">Connecticut</option>
									<option value="DE">Delaware</option>
									<option value="DC">District Of Columbia</option>
									<option value="FL">Florida</option>
									<option value="GA">Georgia</option>
									<option value="HI">Hawaii</option>
									<option value="ID">Idaho</option>
									<option value="IL">Illinois</option>
									<option value="IN">Indiana</option>
									<option value="IA">Iowa</option>
									<option value="KS">Kansas</option>
									<option value="KY">Kentucky</option>
									<option value="LA">Louisiana</option>
									<option value="ME">Maine</option>
									<option value="MD">Maryland</option>
									<option value="MA">Massachusetts</option>
									<option value="MI">Michigan</option>
									<option value="MN">Minnesota</option>
									<option value="MS">Mississippi</option>
									<option value="MO">Missouri</option>
									<option value="MT">Montana</option>
									<option value="NE">Nebraska</option>
									<option value="NV">Nevada</option>
									<option value="NH">New Hampshire</option>
									<option value="NJ">New Jersey</option>
									<option value="NM">New Mexico</option>
									<option value="NY">New York</option>
									<option value="NC">North Carolina</option>
									<option value="ND">North Dakota</option>
									<option value="OH">Ohio</option>
									<option value="OK">Oklahoma</option>
									<option value="OR">Oregon</option>
									<option value="PA">Pennsylvania</option>
									<option value="RI">Rhode Island</option>
									<option value="SC">South Carolina</option>
									<option value="SD">South Dakota</option>
									<option value="TN">Tennessee</option>
									<option value="TX">Texas</option>
									<option value="UT">Utah</option>
									<option value="VT">Vermont</option>
									<option value="VA">Virginia</option>
									<option value="WA">Washington</option>
									<option value="WV">West Virginia</option>
									<option value="WI">Wisconsin</option>
									<option value="WY">Wyoming</option>
								</select>

							@elseif($field_name == 'legal_entity.business_name')
								<label for="postal">Business Name</label>
								<input type="text" id="postal" name="legal_entity.business_name">

							@elseif($field_name == 'legal_entity.dob.day')
								<label for="day">Birth day</label>
								{{ Form::selectRange('stripeAccountData[legal_entity][dob][day]', 1, 31, 'Select birth day...', ['placeholder' => 'Select birth day...', 'autocomplete' => 'off']) }}
							@elseif($field_name == 'legal_entity.dob.month')
								<label for="month">Birth month</label>
								{{ Form::selectMonth('stripeAccountData[legal_entity][dob][month]', 'Select birth month...', ['placeholder' => 'Select birth month...', 'autocomplete' => 'off']) }}
							@elseif($field_name == 'legal_entity.dob.year')
								<label for="month">Birth year</label>
								{{ Form::selectRange('stripeAccountData[legal_entity][dob][year]', 1930, 2016, 'Select birth year...', ['placeholder' => 'Select birth year...', 'autocomplete' => 'off']) }}

							@elseif($field_name == 'legal_entity.first_name')
								<label for="first">First Name</label>
								<input type="text" id="first" name="stripeAccountData[legal_entity][first_name]">
							@elseif($field_name == 'legal_entity.last_name')
								<label for="first">Last Name</label>
								<input type="text" id="first" name="stripeAccountData[legal_entity][last_name]">

							@elseif($field_name == 'legal_entity.ssn_last_4')
								<label for="ssn">Social Security Number (last four digits only)</label>
								<input type="password" id="ssn" name="stripeAccountData[legal_entity][ssn_last_4]" maxlength="4">

							@elseif($field_name == 'legal_entity.type')
								<label for="first">Type</label>
								{{ Form::select('stripeAccountData[legal_entity][type]', ['individual'=>'Individual', 'company'=>'Company']) }}

							@elseif($field_name == 'legal_entity.personal_id_number')
								<label for="first">Personal ID-Number</label>
								<input type="text" id="first" name="stripeAccountData[legal_entity][personal_id_number]">
							@elseif($field_name == 'legal_entity.verification.document')
								<label for="first">Verification Document</label>
								{{ Form::file('verification_document') }}

							@else
								{{$field_name}}
							@endif
						@endforeach

						@if (session('message_success'))
							<span class="invalid_green">{{ session('message_success') }}</span>
						@endif

						@if (session('message_error'))
							<span class="invalid ">{{ session('message_error') }}</span>
						@endif

						<button type="submit">Save</button>
					</form>
				@endif
		</div>
	</div>
@endsection

