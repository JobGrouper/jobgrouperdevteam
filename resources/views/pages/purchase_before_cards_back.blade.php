@extends('layouts.main')

@section('title', 'Payment page')

@section('content')

{{--Это было для автооплат и сохранения карты--}}
{{--<div class="forgotpass">--}}
{{--<div class="forgotpass_form forgot_only">--}}
{{--@if (session('message'))--}}
{{--{{ session('message') }}--}}
{{--@else--}}
{{--<form role="form" method="POST" action="{{ url('/order/store') }}">--}}
{{--{{ csrf_field() }}--}}
{{--<label for="purchase">Select payment card</label>--}}
{{--<select id="purchase" name="credit_card_id">--}}
{{--@if($credit_cards->count() > 0)--}}
{{--@foreach($credit_cards as $credit_card)--}}
{{--<option value="{{$credit_card->id}}">**** **** **** {{$credit_card->last_four}}</option>--}}
{{--@endforeach--}}
{{--@else--}}
{{--<option disabled value="">You have not credit cards</option>--}}
{{--@endif--}}
{{--</select>--}}
{{--<input type="hidden" name="job_id" value="{{$job_id}}">--}}
{{--<div class="invalid_login"></div>--}}
{{--<a href="http://jobgrouper.com/card/create">Add payment card</a>--}}
{{--<button>Purchase</button>--}}
{{--</form>--}}
{{--@endif--}}
{{--</div>--}}
{{--</div>--}}

<div class="creditcard_info">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                @if (session('message'))

                {{ session('message') }}

                @else

                <section class="success">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="success_text">
                                    <img src="{{ asset('img/Success/photo.png') }}" alt="alt">
                                    <p>You have successfully purchased your order!</p>
                                    {{--<a href="{{ $emailUrl }}"><button>Open email</button></a>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="allforms">

                    <div class="tabs">

                        <div class="cards clearfix">

                            <div class="card">

                                <input class="radio" type="radio" checked  name="payment" id="visa">

                                <label for="visa"><img src="{{asset('img/Creditcard/visa.png')}}" alt="alt"> <img src="{{asset('img/Creditcard/mastercard.png')}}" alt="alt"></label>

                            </div>

                            <div class="card">

                                <input class="radio" type="radio" name="payment" id="paypal">

                                <label for="paypal"><img src="{{asset('img/Creditcard/paypal.png')}}" alt="alt"></label>

                            </div>
                        </div>
                        
                    </div>
                    <div class="tabs_content">
                    <form class="creditcard_info__form" role="form" method="POST" id="visaform" action="{{ url('/order/purchase_via_stripe') }}">

                            {{ csrf_field() }}


                            <div class="firstlast">

                                <div class="firstlast__item">

                                    <label for="firstname">First Name</label>

                                    <input type="text" id="firstname" name="first_name">

                                </div>

                                <div class="firstlast__item right">

                                    <label for="lastname">Last Name</label>

                                    <input type="text" id="lastname" name="last_name">

                                </div>

                                <div class="cardnumber">

                                    <label for="cardnumber">Card Number</label>

                                    <input type="text" id="cardnumber" maxlength="16" name="card_number">

                                </div>

                                <div class="datecvv">

                                    <div class="item">

                                        <label>End Date</label>

                                        <select name="end_month" id="endmonth">

                                            <option value="0">mm</option>

                                            @for($i = 1; $i <= 12; ++$i)

                                            <option value="{{$i}}">{{$i}}</option>

                                            @endfor

                                        </select>

                                    </div>

                                    <div class="item">

                                        <select name="end_year" id="endyear">

                                            <option value="0">yyyy</option>

                                            @for($i = 2016; $i <= 2020; ++$i)

                                            <option value="{{$i}}">{{$i}}</option>

                                            @endfor

                                        </select>

                                    </div>

                                    <div class="cvv">

                                        <label for="cvv">CVV</label>

                                        <input type="password" maxlength="3" id="cvv" name="cvv">

                                    </div>

                                </div>

                                <div class="invalid_login"></div>

                                <input type="hidden" name="order_id" value="{{$order->id}}">
                                @if(isset($message))
                                    {{$message}}
                                @endif
                                <button type="submit">Purchase</button>

                            </div>
                        </form>



                        {{--PayPal form for single payment--}}
                        {{--<form class="creditcard_info__form" action="{{$paymentPayPalData['payNowButtonUrl']}}" method="post" id="paypalform">--}}
                            {{--<input type="hidden" name="cmd" value="_xclick">--}}
                            {{--<input type="hidden" name="business" value="{{$paymentPayPalData['receiverEmail']}}">--}}
                            {{--<input id="paypalItemName" type="hidden" name="item_name" value="{{$paymentPayPalData['itemName']}}">--}}
                            {{--<input id="paypalAmmount" type="hidden" name="amount" value="{{$paymentPayPalData['amount']}}">--}}
                            {{--<input type="hidden" name="no_shipping" value="1">--}}
                            {{--<input type="hidden" name="return" value="{{$paymentPayPalData['returnUrl']}}">--}}

                            {{--<input type="hidden" name="custom" value="{{json_encode($paymentPayPalData['customData'])}}">--}}

                            {{--<input type="hidden" name="currency_code" value="USD">--}}
                            {{--<input type="hidden" name="lc" value="US">--}}
                            {{--<input type="hidden" name="bn" value="PP-BuyNowBF">--}}
                            {{--<button type="submit">Purchase</button>--}}
                        {{--</form>--}}
                        {{----}}

                        {{--PayPal form for single payment--}}
                        <form class="creditcard_info__form" id="paypalform" action="{{$paymentPayPalData['payNowButtonUrl']}}" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_xclick-subscriptions">
                            <input type="hidden" name="business" value="{{$paymentPayPalData['receiverEmail']}}">

                            <input id="paypalItemName" type="hidden" name="item_name" value="{{$paymentPayPalData['itemName']}}">
                            <input type="hidden" name="no_note" value="1">
                            <input type="hidden" name="no_shipping" value="1">

                            {{--<input type="hidden" name="return" value="{{$paymentPayPalData['returnUrl']}}">--}}

                            <input type="hidden" name="src" value="1">
                            <input type="hidden" name="a3" value="{{$paymentPayPalData['amount']}}">

                            <input type="hidden" name="p3" value="1">
                            <input type="hidden" name="t3" value="M">

                            <input id="customData" type="hidden" name="custom" value="{{json_encode($paymentPayPalData['customData'])}}">
                            <input type="hidden" name="currency_code" value="USD">

                            <button type="submit">Subscribe</button>
                        </form>
                        {{----}}


                    </div>


                </div>


                @endif

            </div>

        </div>

    </div>

</div>





@endsection