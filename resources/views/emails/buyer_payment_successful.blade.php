@extends('layouts.email')

@section('content')
<p>Your payment to {{ $employee }} on {{ $job->title }} was made successfully.</p>
<table>
	<thead>
	<?php 
		$salary = $job->salary;
	?>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
	  <tr>
		<td>Monthly purchase price</td>
		<td>${{ number_format($salary, 2) }}</td>
	  </tr>
	  <tr>
		<td>Markup</td>
		<td>${{ number_format( $salary * 0.15, 2) }}</td>
	  </tr>
	  <tr>
		<td>Total</td>
		<td>${{ number_format( $salary + ( $salary * 0.15), 2) }}</td>
	  </tr>
	</tbody>
	<p>Please keep this receipt for your records.</p>
</table>
@endsection

