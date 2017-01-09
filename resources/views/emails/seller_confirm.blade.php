@extends('layouts.email')

@section('content')
<h1>Final Steps for Verification</h1>
<p>Now that you've activated your account, there is one final step needed
for you to get paid for the amazing work you do at JobGrouper!</p>

<p>To receive monthly payments, please proceed to your <a href="{{ env('SERVICE_APP_URL') }}/account">
account page</a> as soon as possible and add a payment method to your profile;
you can choose to be paid through your bank account or through a debit
card. As soon as you do that, we will mark your profile as fully
verified. If we require any additional information from you, we'll
notify you as quickly as possible.</p>

<p>One great thing about JobGrouper is that you'll never need to run
around collecting money from all your buyers each month. This also means that buyers will never need to know your
financial details, which are securely encrypted on our site.</p>

<p>Thanks for working with us!</p>

<p>--JobGrouper</p>
@endsection
