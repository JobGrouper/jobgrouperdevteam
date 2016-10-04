@extends('layouts.main')

@section('title', 'Transactions archive')

@section('content')



    <div class="creditcard_info">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <p>Transactions Archive</p>

                    <div class="payments">
                        <div class="payments_head">
                            <h2>Transactions Archive</h2>
                        </div>
                        <div class="payments_content">
                            @if($payments->count() > 0)
                            <table>
                                <thead>
                                <tr>
                                    <td>Transaction ID</td>
                                    <td>Project Name</td>
                                    <td>For Month</td>
                                    <td>Data</td>
                                    <td>Payment System</td>
                                    <td>Amount</td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{$payment->transaction_id}}</td>
                                    <td>{{$payment->order()->first()->job()->first()->title}}</td>
                                    <td>{{ date('M', mktime(0, 0, 0, $payment->month, 1, 2000))}}</td>
                                    <td>{{$payment->formated_created_at}}</td>
                                    <td>{{($payment->payment_system == 'stripe' ? 'CreditCard' : 'PayPal')}}</td>
                                    <td>${{$payment->amount}}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                                <span class="noyet">No payments yet</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>



@endsection

