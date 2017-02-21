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
		<div class="adjuster_elem clearfix">
			<button class="min-down">Down</button>
			<input type="text" class="min-input" name="new_client_min" value="{{ $job->min_clients_count }}">
			<button class="min-up">Up</button>
		</div>
		</div>
		<div class="adjuster_elem clearfix">
		<p>Max Buyers</p>
		<div>
			<button class="max-down">Down</button>
			<input type="text" class="max-input" name="new_client_max" value="{{ $job->max_clients_count }}">
			<button class="max-up">Up</button>
		</div>
		</div>
	</div>
	<div>
		<input type="hidden" class="ba_job_id_field" name="job_id" value="{{ $job->id }}"/>
		<input type="hidden" name="current_client_min" value="{{ $job->min_clients_count }}"/> 
		<input type="hidden" name="current_client_max" value="{{ $job->max_clients_count }}"/> 
		@if($purpose == 'request' || $purpose == 'admin-from-request')
		<input type="hidden" name="employee_id" value="{{ $employee->id }}"/> 
		@endif
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
