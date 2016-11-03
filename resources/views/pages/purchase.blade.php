@extends('layouts.main')

@section('title', 'Payment page')

@section('content')
    <div class="alert_window month">
        <div class="alert_window__block">
            <p></p>
            <p>Monthly price of the purchase:  USD</p>
            <p>Markup:  USD</p>
            <p>Total:  USD</p>
            <div class="cancel"></div>
        </div>
    </div>
    <div class="forgotpass">
        <div class="forgotpass_form forgot_only">
            @if (session('message'))
                {{ session('message') }}
            @else
                <form role="form" method="POST" action="{{ ($changeCard ? url('/change_credit_card') : url('/order')) }}">
                    {{ csrf_field() }}
                    <label>Summary</label>
                    <p>Monthly price of the purchase: <span>{{$job->salary}} USD</span></p>
                    <p>Markup: <img src="{{asset('img/View/circle.png')}}" alt="alt"><span>{{$job->salary*0.15}} USD</span></p>
                    <p>Total: <span>{{$job->salary + ($job->salary*0.15)}} USD</span></p>
                    <input name="_method" type="hidden" value="PUT">
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
                    <input type="hidden" name="order_id" value="{{$order->id}}">
                    <div class="invalid_login"></div>
                    <p>Payments for all subsequent months of work will be done automatically using this card.</p>
                    <a href="{{url('card/create')}}">Add payment card</a>
                    <button>{{ ($changeCard ? 'Change credit card' : 'Purchase') }}</button>
                </form>
            @endif
        </div>
    </div>





@endsection
