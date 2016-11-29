@extends('layouts.main')

@section('title', 'Payment page')

@section('content')
    <div class="forgotpass">
        <div class="forgotpass_form forgot_only">
            @if (session('message'))
                {{ session('message') }}
            @else
		    <!--
                    <form class="" role="form" method="POST" action="{{ url('/card/store') }}">
                            {{ csrf_field() }}
                    </form>
			-->
                <form role="form" method="POST" action="{{ ($changeCard ? url('/change_credit_card') : url('/order')) }}">
                    {{ csrf_field() }}
			<!--- SET CARD START -->
                        <div class="firstlast">
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
                            @if (session('message_success'))
                                <span class="invalid_green">{{ session('message_success') }}</span>
                            @endif
                            @if (session('message_error'))
                                <span class="invalid">{{ session('message_error') }}</span>
                            @endif
                        </div>
			<!-- SET CARD END -->
                    <input name="_method" type="hidden" value="PUT">
			<!--
                    <label for="purchase">Select payment card</label>
                    <select id="purchase" name="credit_card_id">
                        @if($creditCards->count() > 0)
                            @foreach($creditCards as $creditCard)
                                <option value="{{$creditCard->id}}">{{$creditCard->number}}</option>
                            @endforeach
                        @else
                            <option disabled value="">You have no credit cards</option>
                        @endif
                    </select>
			-->
                    <input type="hidden" name="order_id" value="{{$order->id}}">
                    <div class="invalid_login"></div>
                    {!! ($changeCard ? false : ' <p>The first month will be paid.</p>') !!}
                    <p>Payments for next months of work will be done automatically using this card.</p>
                    <!--<a href="{{url('card/create')}}">Add payment card</a>-->
                    <button>{{ ($changeCard ? 'Change credit card' : 'Purchase') }}</button>
                </form>
            @endif
        </div>
    </div>





@endsection
