<div id="buyer_adjuster">
<form id="buyer-adjuster-form">
	<div>
		<div>Buyers: <span class="sales-count">{{ count($orders) }}</span>/{{ $job->max_clients_count }}</div>
		<div>Min to Start: {{ $job->min_clients_count }}</div>
	</div>
	@if($purpose == 'admin-from-request')
	<div>
		<div>Requested Min: O</div>
		<div>Requested Max: P</div>
	</div>
	@endif
	<div>
		<div>
		<p>Min Buyers</p>
		<div>
			<button class="min-down">Down</button>
			<input type="text" class="min-input" name="min_clients_count" value="{{ $job->min_clients_count }}">
			<button class="min-up">Up</button>
		</div>
		</div>
		<div>
		<p>Max Buyers</p>
		<div>
			<button class="max-down">Down</button>
			<input type="text" class="max-input" name="max_clients_count" value="{{ $job->max_clients_count }}">
			<button class="max-up">Up</button>
		</div>
		</div>
	</div>
	<div>
		<input type="hidden" class="ba_job_id_field" name="job_id" value="{{ $job->id }}"/>
		<input type="hidden" name="employee_id" />
		@if($purpose == 'request')
		<button id="request-start-work-button">Start Work Now</button>
		<input id="request-submit-button" type="submit" value="Submit" />
		@else
		<button id="buyer-adjuster-start-work-button">Start Work Now</button>
		<input id="buyer-adjuster-submit-button" type="submit" value="Submit" />
			@if($purpose == 'admin-from-request')
			<button id="deny-request-button">Deny Request</button>
			@endif
		@endif
	</div>
</form>
</div>
