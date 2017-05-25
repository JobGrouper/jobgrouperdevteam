@extends('layouts.email')

@section('content')
    {{ $employee->full_name }} has accepted your request for "early bird" access to their services at the increased rate of ${{$job->early_bird_markup}} per month.
    You can communicate with {{ $employee->full_name }} through your My Orders page.
    <br>
    Other buyers may also purchase early bird services, when that happens, the increased rate that you pay will decrease and these early bird buyers will count toward the minimum number of buyers required to begin work officially.
@endsection