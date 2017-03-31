@extends('layouts.email')

@section('content')
<p>Unfortunately, Stripe, our payments provider, has notified us that a scheduled transfer in the 
amount of {{ amount }} has failed. This may be due to a number of factors, such as an 
account number, routing number, or accountholder name entered incorrectly in your profile 
section. Please click here[link to bank details in profile] to edit your information.</p>

<p>After you re-submit your information, Stripe will once again attempt to transfer funds 
to your account within ___ business days. Please reach out to us at 
support@jobgrouper.com with any questions or concerns.</p>
@endsection
