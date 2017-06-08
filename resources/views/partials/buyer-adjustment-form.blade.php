<div id="buyer_adjuster">
<form id="buyer-adjuster-form">
	<div class="adjustment-info">
		<div class="info-cubby">Buyers: <span class="sales-count">{{ count($purchases) }}</span>/<span class="current-max">{{ $job->max_clients_count }}</span></div>
		<div class="border"></div>
		<div class="info-cubby">Min to Start: <span class="current-min">{{ $job->min_clients_count }}</span></div>
	</div>
	@if($purpose == 'admin-from-request')
	<div class="adjustment-info">
		<div class="info-cubby">Requested Min: <span>{{ $request->requested_client_min }}</span></div>
		<div class="border"></div>
		<div class="info-cubby">Requested Max: <span>{{ $request->requested_client_max }}</span></div>
	</div>
	@endif
	<div class="adjustment-tools">
		<div>
		<div class="adjuster_elem clearfix">
		<h4>Min Buyers</h4>
		<div>
			<button class="min-down">&#9668</button>
			<input type="text" class="min-input" name="new_client_min" value="{{ $job->min_clients_count }}">
			<button class="min-up">&#9658</button>
		</div>
		</div>
		<div class="adjuster_elem clearfix">
		<h4>Max Buyers</h4>
		<div>
			<button class="max-down">&#9668</button>
			<input type="text" class="max-input" name="new_client_max" value="{{ $job->max_clients_count }}">
			<button class="max-up">&#9658</button>
		</div>
		</div>
		</div>
	</div>
	<div class="loading"></div>
	<div class="adjustment-message">
		<p id="ba-message-field"></p>
	</div>
	<div class="adjustment-submit">
		<input type="hidden" class="ba_job_id_field" name="job_id" value="{{ $job->id }}"/>
		<input type="hidden" name="current_client_min" value="{{ $job->min_clients_count }}"/> 
		<input type="hidden" name="current_client_max" value="{{ $job->max_clients_count }}"/> 
		@if($purpose == 'admin-from-request')
		<input type="hidden" class="ba_request_id_field" name="request_id" value="{{ $request->id }}"/> 
		@endif
		@if($purpose == 'request' || $purpose == 'admin-from-request')
		<input type="hidden" name="employee_id" value="{{ $employee->id }}"/> 
		@endif
		@if($purpose == 'request')
		<button id="request-start-work-button">Start Work Now</button>
		<input id="request-submit-button" type="submit" value="Submit" />
		@else
		<button id="buyer-adjuster-start-work-button">Start Work Now</button><div></div>
		<input id="buyer-adjuster-submit-button" type="submit" value="Submit" />
			@if($purpose == 'admin-from-request')
			<input id="buyer-adjuster-deny-request-button" class="deny" type="submit" value="Deny Request"/>
			@endif
		@endif
	</div>
	<hr>
	<div class="adjustment-links">
		@if($purpose == 'admin-from-request')
		<a href="/admin/cards">Back to Cards</a>
		@endif
		@if($purpose == 'admin')
		<a href="/admin/card/{{ $job->id }}/edit">Back to edit page</a>
		@endif
	</div>
</form>
</div>
