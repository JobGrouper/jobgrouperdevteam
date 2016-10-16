@extends('layouts.main')

@section('title', 'Payment page')

@section('content')
    <div class="forgotpass">
        <div class="forgotpass_form forgot_only">
            @if (session('message'))
                {{ session('message') }}
            @else
                <form role="form" method="POST" action="{{ ($changeCard ? url('/change_credit_card') : url('/order')) }}">
                    {{ csrf_field() }}
                    <input name="_method" type="hidden" value="PUT">
                    <label for="purchase">Select payment card</label>
                    <select id="purchase" name="credit_card_id">
                        @if($creditCards->count() > 0)
                            @foreach($creditCards as $creditCard)
                                <option value="{{$creditCard->id}}">{{$creditCard->number}}</option>
                            @endforeach
                        @else
                            <option disabled value="">You have not credit cards</option>
                        @endif
                    </select>
                    <input type="hidden" name="order_id" value="{{$order->id}}">
                    <div class="invalid_login"></div>
                    {!! ($changeCard ? false : ' <p>The first month will be paid.</p>') !!}
                    <p>Payments for next months of work will be done automatically using this card.</p>
                    <a href="{{ Request::root() }}/card/create">Add payment card</a>
                    <button>{{ ($changeCard ? 'Change credit card' : 'Purchase') }}</button>
                </form>
            @endif
        </div>
    </div>





@endsection
