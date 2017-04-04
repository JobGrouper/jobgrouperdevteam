@extends('layouts.email')

@section('content')
<p>Unfortunately, Stripe, our payments provider, has notified us that a scheduled transfer in the 
amount of {{ data.amount }} has failed. This may be due to a number of factors, such as an 
account number, routing number, or account holder name entered incorrectly in your profile 
section. <a href='{{ env("SERVICE_APP_URL") }}/account'>Please click here</a> to edit your information.</p>

<p>After you re-submit your information, Stripe will once again attempt to transfer funds 
to your account. You'll receive further emails as the transfer proceeds. Please reach out to us at 
support@jobgrouper.com with any questions or concerns.</p>
@endsection
