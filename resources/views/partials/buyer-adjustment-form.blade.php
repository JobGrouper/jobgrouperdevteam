<div id="buyer_adjuster">
<form>
	<div>
		<div>Buyers: <span class="sales-count">{{ count($orders) }}</span>/{{ $job->max_clients_count }}</div>
		<div>Min to Start: {{ $job->min_clients_count }}</div>
	</div>
	@if($requested)
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
			<input type="text" class="min-input" value="{{ $job->min_clients_count }}">
			<button class="min-up">Up</button>
		</div>
		</div>
		<div>
		<p>Max Buyers</p>
		<div>
			<button class="max-down">Down</button>
			<input type="text" class="max-input" value="{{ $job->max_clients_count }}">
			<button class="max-up">Up</button>
		</div>
		</div>
	</div>
	<div>
		<input type="hidden" name="job_id" value="{{ $job->id }}"/>
		<input type="hidden" name="employee_id" />
		<button>Start Work Now</button>
		<input type="submit" value="Submit" />
	</div>
</form>
</div>
