@extends('layouts.main')

@section('content')

<div class="creditcard_info">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <p>Credit card info</p>



                    <form class="creditcard_info__form" role="form" method="POST" action="{{ url('/card/store') }}">

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

                            @if (session('message_success'))

                                <span class="invalid_green">{{ session('message_success') }}</span>

                            @endif

                            @if (session('message_error'))

                                <span class="invalid">{{ session('message_error') }}</span>

                            @endif

                            <button type="submit">Add card</button>
                            

                        </div>

                    </form>

            </div>

        </div>

    </div>

</div>



@endsection