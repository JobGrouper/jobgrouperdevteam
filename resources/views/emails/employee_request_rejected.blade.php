@extends('layouts.email')

@section('content')
<p>Your application for the "{{$job_name}}" position has been denied.</p>

<p>
Please don't take it personally. We carefully reviewed 
many applications and we had to turn away a lot of 
promising candidates.
</p>

<p>
Please feel free to apply for other positions on the site � 
and even the same position, if we post it again!
</p>

<p>Best of luck,</p>

<p>--JobGrouper</p>
@endsection