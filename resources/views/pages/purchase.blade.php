@extends('layouts.main')

@section('title', 'Payment page')

@section('content')
    <div class="alert_window month">
        <div class="alert_window__block">
            <p></p>
            <p>Monthly purchase price:  {{$job->salary}} USD</p>
            <p>Markup:  {{$job->salary}} * 15% fee = {{$job->salary*0.15}} USD</p>
            <p>Total:  {{$job->salary + ($job->salary*0.15)}} USD</p>
            <div class="cancel"></div>
        </div>
    </div>
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
                    <label>Summary</label>
                    <p>Monthly purchase price: <span>${{number_format($job->salary, 2)}}</span></p>
                    <p>Markup: <img src="{{asset('img/View/circle.png')}}" alt="alt"><span>${{number_format( $job->salary*0.15, 2)}}</span></p>
                    <p>Total: <span>${{number_format( $job->salary + ($job->salary*0.15), 2)}}</span></p>
		    <hr>

                <form role="form" method="POST" action="{{ ($changeCard ? url('/change_credit_card') : url('/order')) }}">
                    {{ csrf_field() }}
			<!--- SET CARD START -->
                        <div class="firstlast">
                            <div class="cardnumber">
                                <label for="cardnumber">Card Number</label>
                                <input type="text" id="cardnumber" maxlength="16" name="card_number" autocomplete="off">
                            </div>
                            <div class="datecvv">
                                <div class="item">
                                    <label>End Date</label>
                                    <select name="exp_month" id="expmonth" autocomplete="off">
                                        <option value="0">mm</option>
                                        @for($i = 1; $i <= 12; ++$i)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="item">
                                    <select name="exp_year" id="expyear" autocomplete="off">
                                        <option value="0">yyyy</option>
                                        @for($i = 2016; $i <= 2030; ++$i)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="cvc">
                                    <label for="cvc">CVC</label>
                                    <input type="password" maxlength="3" id="cvc" name="cvc" autocomplete="off">
                                </div>
                            </div>
                            @if (session('message_success'))
                                <span class="invalid_green">{{ session('message_success') }}</span>
                            @endif
                            @if (session('message_error'))
                                <span class="invalid">{{ session('message_error') }}</span>
                            @endif

			    @foreach ($errors->all() as $error)
				<p>** {{ $error }} **</p>
			    @endforeach
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
                    <p>Payments for all subsequent months of work will be done automatically using this card.</p>
                    <!--<a href="{{url('card/create')}}">Add payment card</a>-->
                    <button>{{ ($changeCard ? 'Change credit card' : 'Purchase') }}</button>
                </form>
            @endif
        </div>
    </div>





@endsection
