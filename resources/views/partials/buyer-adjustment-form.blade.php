<div>
<form>
	<div>
		<div>Buyers: n/N</div>
		<div>Min to Start</div>
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
			<button></button>
			<input type="text">
			<button></button>
		</div>
		</div>
		<div>
		<p>Max Buyers</p>
		<div>
			<button></button>
			<input type="text">
			<button></button>
		</div>
		</div>
	</div>
	<div>
		<input type="hidden" name="job_id" />
		<input type="hidden" name="employee_id" />
		<button>Start Work Now</button>
		<input type="submit">Submit</input>
	</div>
</form>
</div>
