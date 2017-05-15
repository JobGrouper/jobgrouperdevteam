@extends('layouts.email')

@section('content')
<p>A payout for your work, in the amount of ${{ $data['amount'] }}, is on its way! 
It should arrive on {{ $data['arrival_date'] }}</p>
<p>If this is your first payout from JobGrouper, the payout may be delayed for routine processing;
up to seven days. All subsequent payouts should proceed on the normal schedule. 
Please be in touch with us at support@jobgrouper.com if you do not receive your funds for any 
reason. We will be sending further emails as the process continues.</p>
@endsection
