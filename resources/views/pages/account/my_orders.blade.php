@extends('layouts.main')

@section('title', 'My Orders')

@section('autoload_scripts')

<script>
	var early_birds = [];
	var early_bird_activator;

	jg.Autoloader(function() {

		early_bird_activator = new jg.EarlyBirdActivator({

		});
	});
</script>

@endsection

@section('content')

<div class="myjobs">

    <div class="container">

        <div class="row">

            <p class="title">My Orders</p>

            <div class="workers">

                @if(count($orders) > 0)
                    @foreach($orders as $order)
                        <?php
                            $job = $order->job()->first();
                            $employee = $job->employee()->first();
                            $closeRequest = $order->close_order_requests()->where('originator_id', '=', $buyer->id)->first();

                        ?>

        	    <div class="alert_window early_bird_job" job_id="{{ $job->id }}">
            		<div class="alert_window__block">
				<p>Hello</p>
                		<div class="cancel"></div>
			</div>
		    </div>

                    <div class="workers_item clearfix" data-id="{{$order->id}}" id="block_{{$order->id}}" data-hasEmployee="<?=($employee ? $employee->id : '0')?>">

                        <div class="img_wrapper">
                            @if($employee)
                                <img src="{{ asset($employee->image_url) }}" alt="alt">
                            @endif
                        </div>

                        <div class="rating">
                            @if($employee)
                                <div class="stars"><div class="yellow"></div></div>
                            @endif
                            <div class="rating_name">
                                @if($employee)
                                    <span>{{$employee->fullname}}</span>
                                @else
                                    <span class="no_employee">No employee yet<span>
                                @endif

                            </div>
                            @if($employee)
                                <a href="/messages/{{$employee->id}}">message ({{$buyer->getNewMessages($employee->id)}} new)</a>
                            @endif

                        </div>

                        <div class="salary">

                            <p><a href="/job/{{$job->id}}">{{$job->title}}</a></p>

                            <p class="month">{{$job->description}}</p>

                        </div>

                        <div class="order_salary">

                            <p>${{$job->salary}}/month</p>

                            {{--<p class="month">work {{$order->created_at->diffInMonths() + 1}} month{{(($order->created_at->diffInMonths() + 1) > 1 ? 's' : '') }}</p>--}}
				<!--
                            <p class="month">
                                @if($order->paid_to)
                                    Paid to {{$order->paid_to}}
                                @else
                                    Needs Payment!<br>
                                    This job will time out in {{Carbon\Carbon::now()->diffInMinutes(Carbon\Carbon::parse($order->updated_at)->addMinutes(5)) }} minutes
                                @endif
                            </p>
				-->
                        </div>

                        {{--@if($closeRequest)--}}
                        @if($order->status == 'closed')
                            {{--<button class="Request">Request Sent</button>--}}
                            <button class="Request">Order closed</button>
                        @else
                            {{--<a href="/purchase/{{$order->id}}"><button class="purchasebtn">Purchase for {{ date('M', mktime(0, 0, 0, $order->month_to_pay, 1, 2000))}}</button></a>--}}

                            @if($order->card_set)
                                <!--<p class="credit_card"><span class="wrap"><span style="font-weight: 700">Credit Card:</span> <span class="number"></span></span><a href="/change_credit_card/{{$order->id}}">Change card</a></p>-->
				@if($early_bird)
				   	@if($early_bird->status == 'requested')
					   <button class="early-bird-request-pending">Request Pending</button>
					@elseif($early_bird->status == 'denied')
					   <button class="early-bird-request-denied">Request Denied</button>
					@elseif($early_bird->status == 'working')
					   <button class="early-bird-working">Currently Working</button>
					   <button class="early-bird-end-work">Cancel Early Work</button>
					@elseif($early_bird->status == 'ended')
					   <button class="early-bird-ended">Early Bird Ended</button>
				   	@endif
				@else
					@if($job->employee_id != NULL)
					   <button class="early-bird-buy-now" job_id={{$job->id}}>Buy Now</button>
					@endif
				@endif
                            @elseif(!$order->card_set && $job->employee_id == NULL)
                                <button class="purchasebtn">Waiting on Employee</button>
			    @elseif(!$order->card_set && $job->employee_id != NULL)
                                <a href="/purchase/{{$order->id}}"><button class="purchasebtn">Confirm</button></a>
                            @endif

                            <form role="form" method="POST" action="{{ url('/order/close/' . $order->id ) }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="order_id" value="{{$order->id}}">
                               <!-- <a class="popup-with-move-anim" href="#small-dialog4"><button type="submit" class="close_order_btn cancelbtn" data-order_id="{{$order->id}}">Close order</button><button class="Request2">Order closed</button></a> -->
				<button type="submit" class="close_order_btn cancelbtn" data-order_id="{{$order->id}}">Close order</button><button class="Request2">Order closed</button>
                            </form>
                            {{--<button class="Request">Request Sent</button>--}}

                        @endif

                    </div>

                    @endforeach

                @else
                    You do not have any orders. You can order a job <a href="jobs">here</a>.
                @endif

            </div>

        </div>

    </div>

</div>

`

@endsection
