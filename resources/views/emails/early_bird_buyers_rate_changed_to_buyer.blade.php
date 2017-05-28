@extends('layouts.email')

@section('content')

<p>The rate you have been paying since your first monthly purchase of {{ $data['job']->title }} has now 
been changed to {{ number_format($data['job']->early_bird_markup, 2) }}.</p>

<p>When the minimum amount of buyers for this job has been reached, the price will revert to its 
typical level. In the meantime, if early bird buyers continue to make purchases, the monthly 
payment will gradually reduce until the minimum number of buyers has been reached.</p>
@endsection
