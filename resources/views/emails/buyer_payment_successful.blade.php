@extends('layouts.email')

@section('content')
<p>Your payment to {{ $employee }} on {{ $job->title }} was made successfully.</p>
<table>
	<thead>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
	  <tr>
		<td>Monthly purchase price</td>
		<td>${{ number_format($job->salary, 2) }}</td>
	  </tr>
	  <tr>
		<td>Markup</td>
		<td>${{ number_format( $job->markup, 2) }}</td>
	  </tr>
	  <tr>
		<td>Total</td>
		<td>${{ number_format( $job->salary + $job->markup , 2) }}</td>
	  </tr>
	</tbody>
</table>
<p>Please keep this receipt for your records.</p>
@endsection

