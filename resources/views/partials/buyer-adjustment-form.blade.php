<div id="buyer_adjuster">
	<span class="close__btn">X</span>
	<form method="POST">
		<div>
			<div>Buyers: <span class="sales-count">{{$job->sales_count}}</span>/{{ $job->max_clients_count }}</div>
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
			<div class="adjuster_elem clearfix">
				<button class="min-down">Down</button>
				<input type="text" name="new_client_min" class="min-input" value="{{ $job->min_clients_count }}">
				<button class="min-up">Up</button>
			</div>
			</div>
			<div>
			<p>Max Buyers</p>
			<div class="adjuster_elem clearfix">
				<button class="max-down">Down</button>
				<input type="text" class="max-input" name="new_client_max" value="{{ $job->max_clients_count }}">
				<button class="max-up">Up</button>
			</div>
			</div>
		</div>
		<div>
			<input type="hidden" class="job__id" name="job_id" value="{{ $job->id }}"/>
			<input type="hidden" name="employee_id" />
			<button class="start">Start Work Now</button>
			<input class="start" type="submit" value="Submit" />
		</div>
	</form>
</div>
