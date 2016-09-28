@extends('layouts.admin')



@section('title', 'LIST OF ORDERS')



@section('content')

    <div class="content_form">

        <div class="userslist_wrapper">

            @foreach($orders as $order)

                <?php

                    $buyer = $order->buyer()->first();

                ?>

                <div class="userslist_wrapper__item">

                    <div class="img_wrapper"><a href="/account/{{$buyer->id}}" target="_blank"><img src="{{$buyer->image_url}}" alt="alt"></a></div>

                    <span class="name">{{$buyer->fullname}}</span>

                </div>

            @endforeach

        </div>

    </div>
@stop