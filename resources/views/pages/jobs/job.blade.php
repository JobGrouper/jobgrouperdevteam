@extends('layouts.main')

@section('title', $job->title)

@section('autoload_scripts')

<script>
	var buyer_adjuster;
	var seller_activator;
	jg.Autoloader(function() {

	@if($employeeRequest)
	    @if($employeeRequest->status == 'approved' && !$adjustment_request)

		buyer_adjuster = new jg.BuyerAdjuster({
			root: document.getElementById('buyer_adjuster'),
			modal: {
				root: document.getElementById('buyer_adjustment_alert_window'),
				trigger: document.getElementById('buyer-adjustment-alert-button')
			},
			request: true
		});

	   @endif
	@endif

		seller_activator = new jg.EarlyBirdActivator({

		});
	});
</script>

@endsection

@section('content')

    <div class="view">
        @if(!Auth::guest() && Auth::user()->user_type == 'employee')
        <div class="alert_window month">
            <div class="alert_window__block">
                <p>{!! $pageTexts[13] !!}</p>
                <h2>${{ number_format($job->monthly_salary * 12, 2)}}</h2>
                <div class="cancel"></div>
            </div>
        </div>
	
	<!-- NESTED IF -->
	@if($employeeRequest)
	    @if($employeeRequest->status == 'approved')
		<div id="buyer_adjustment_alert_window" class="alert_window">
		    <div class="alert_window__block">
			@include('partials.buyer-adjustment-form', ['purpose' => 'request'])
			<div class="cancel"></div>
		    </div>
		</div>
	    @endif
	@endif
	@else
        <div class="alert_window month">
            <div class="alert_window__block">
		<p>JobGrouper applies a 15 percent service fee on top of the listed price</p>
		<hr>
                <p>Monthly price: ${{number_format($job->salary, 2)}}</p>
                <p>Markup: ${{number_format($job->salary, 2)}} * 15% fee = ${{number_format( $job->salary*0.15, 2)}}</p>
                <p>Total: ${{number_format( $job->salary + ($job->salary*0.15), 2)}}</p>
                <div class="cancel"></div>
            </div>
        </div>
        @endif

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <div class="view_content">

                        <div class="view_content__title">{{$category->title}}</div>

                        <h1>{{$job->title}}</h1>

                        <img src="{{ asset($job->image_url)}}" alt="alt">

                        <h2>Description</h2>

                        <p>{!! nl2br($job->description) !!}</p>

                    </div>

                    <div class="view_sidebar">
                        @if($employee)

                        <div class="user">

                            <div class="img_wrapper">

                                <a href="/account/{{$employee->id}}"><img src="{{ asset($employee->image_url)}}" alt="alt"></a>

                            </div>

                            <div class="social">

                                <p class="name"><a href="/account/{{$employee->id}}">{{$employee->full_name}}</a></p>

                @if($employee['fb_url'])
				    <div class="fb">
                        <img src="{{asset('img/Profile/fb.png')}}" alt="alt">
                            <a href="https://www.facebook.com/{{ $employee['fb_url'] }}">Facebook</a>
				        </img>
				    </div>
                @endif

                @if($employee['linkid_url'])
                    <div class="twitter">
                        <img src="{{ asset('img/Profile/link.png') }}" alt="alt">
                            <a href="http://www.linkedin.com/in/{{ $employee['linkid_url'] }}">LinkedIn</a>
                        </img>
                    </div>
                @endif

                @if($employee['git_url'])
				<div class="twitter">
                    <img src="{{ asset('img/Profile/github.png')}}" alt="alt">
                          <a href="https://github.com/{{ $employee['git_url'] }}">Github</a>
                    </img>
				</div>
                @endif

                                <a href="/account/{{$employee->id}}">More details</a>
                                @if($employeeStatus['status'] == 'leave')
                                    <span>Employee will leave this job at {{$employeeStatus['leave_date']}}</span>
                                @endif

                            </div>

                        </div>

                        @endif

                        <h2>{!! $pageTexts[11] !!}</h2>

                        <div class="salary_info">

                            <div class="purchase">

                                <div class="block bordered">

                                    <span class="amount">${{$job->getConfiguredSale($user)}}/mo</span>
				    
				    @if(!isset($user) || $user->user_type == 'buyer')
				    <span class="disclaimer">A service charge will be added on to the purchase price</span>
				    @endif

                                </div>

                                <div class="block orange">

                                    <span class="purchased">Work starts on the {{$job->min_clients_ordinal}} purchase</span>

				    </br>
                                    <span class="dole">Purchased:</span>
                                    <span class="purchased">{{$job->sales_count}}/{{$job->max_clients_count}}</span>

                                </div>

                            </div>

                        </div>

                        <div class="rangeslider">

                            <!-- <div class="rangeslider_line">123</div> -->

                        </div>

                        <div class="statebuttons">
                            <p class="pending">Your request is pending</p>

                            <p class="approved">You got the job!</p>

                            <p class="rejected">Your request rejected</p>

                        </div>

                        <div class="buttons">
                            <div class="loading"></div>
                            <div class="job_button_error center"></div>

                            @if (Auth::guest())
			       @if( $job->sales_count < $job->max_clients_count )
                                <a href="/login"><button>Buy</button></a>
                                @if(!$employee || $employeeStatus['status'] == 'leave')
                                    {{--If job has no employee or employee will leave this job--}}
                                    <a href="/login"><button class="apply noclick">Apply for this Job</button></a>
                                @endif
			       @endif
                            @elseif(Auth::user()->user_type == 'employee')
                                @if($employeeRequest)
                                    @if($employeeRequest->status == 'pending')
                                            <span class="pending">Your request is pending</span>
                                    @elseif($employeeRequest->status == 'approved')
                                        <span class="approved">You got the job!</span>
					@if(!$adjustment_request)
					<button id="buyer-adjustment-alert-button" class="nostyleyet">Request Buyer Adjustment</button>
					@else
                                        <span id="buyer-adjustment-alert-button" class="pending">Your request is pending</span>
					@endif
                                    @elseif($employeeRequest->status == 'rejected' && !$employee)
                                        {{--If employee`s request has been rejected--}}
                                        <button class="apply">Re-apply for this Job</button>
                                    @endif
                                @else
                                    @if(!$employee || $employeeStatus['status'] == 'leave')
                                        {{--If job has no employee or employee will leave this job--}}
                                        <button class="apply">Apply for this Job</button>
                                    @endif
                                @endif
                            @else
                                @if($jobPaid)
                                    <span class="approved">YOU HAVE ORDERED THIS JOB</span>
                                @elseif($jobOrdered && $job->employee_id != NULL)
                                    <a href="/purchase/{{ $user_order_info->id }}"><span class="approved need">PLEASE CONFIRM ORDER</span></a>
                                @elseif($jobOrdered && $job->employee_id == NULL)
                                    <span class="approved need">Waiting For Employee</span>
                                @else
                                    @if($job->sales_count < $job->max_clients_count)
                                        <form role="form" method="POST" action="{{ url('/order/store') }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="job_id" value="{{$job->id}}">
                                            <button type="submit">Order</button>
                                        </form>
                                    @endif
                                @endif
                            @endif

                        </div>

                        <div class="recent">

                            @if(count($orders) > 0)

                                <h2>{!! $pageTexts[12] !!}</h2>

                                <div class="wrapper">

                                @foreach($orders as $order)
                                        <?php
                                            $buyer = $order->buyer()->first();
                                        ?>

					@if($buyer)
                                        <div class="recent_item">

                                            <div class="img_wrapper">

                                                <a href="/account/{{$buyer->id}}"><img src="{{ asset($buyer->image_url)}}" alt="alt"></a>

                                            </div>

                                            <div class="text">

                                                <h4><a href="/account/{{$buyer->id}}">{{$buyer->full_name}}</a></h4>

                                                <p class="join">Ordered service</p>

                                                <p class="time">{{$order->created_at}}</p>

                                            </div>

                                        </div>
					@endif

                                @endforeach

                                </div>

                            @else
                                <h2>{!! $pageTexts[12] !!}</h2>

                                <span class="nomess">no information yet</span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

            @if(!Auth::guest() && Auth::user()->user_type == 'employee')
	    <div class="row job_buyers">
		@if( count($orders) > 0 )
			<h3>Buyers</h3>
			<hr>
		@else
			<p>No buyers have signed up yet</p>
		@endif
		
		@foreach($orders as $order)
		    <div class="buyer">
			<div class="info">
				    <div class="img_wrapper">
					<a href="/account/{{$order->buyer->id}}"><img src="{{ asset($order->buyer->image_url)}}" alt="alt"></a>
				    </div>

				    <div class="text">
					<h4><a href="/account/{{$order->buyer->id}}">{{$order->buyer->full_name}}</a></h4>
				    </div>
			</div>
			<div class="jgpanel">
			@if( isset( $order->early_bird_buyer ) )
				@if( $order->early_bird_buyer->status == 'requested')
				<div class="center">
					<div class="question" user_id="{{ $order->buyer->id }}">
						<p>This user has asked to start work now.</p>
						
						<form class="early_bird_accept_form" user_id="{{ $order->buyer->id }}">
							<input type="hidden" name="job_id" value="{{ $job->id }}"/>
							<input type="hidden" name="user_id" value="{{ $order->buyer->id }}"/>
							<input type="hidden" name="early_bird_buyer_id" value="{{ $order->early_bird_buyer->id }}"/>
							<button class="early_bird_agree" user_id="{{ $order->buyer->id }}">Okay</button>
							<button class="early_bird_deny" user_id="{{ $order->buyer->id }}">No, thanks</button>
						</form>
					</div>
					<div class="loading" user_id="{{ $order->buyer->id }}"></div>
					<p class="early_bird_error hidden" user_id="{{ $order->buyer->id }}"></p>
				</div>
				@elseif( $order->early_bird_buyer->status == 'working' )
				<div class="center">
					<div class="question" user_id="{{ $order->buyer->id }}">
						<p>This user has purchased early access.</p>
						<form class="early_bird_cancel_form" user_id="{{ $order->buyer->id }}">
							<input type="hidden" name="job_id" value="{{ $job->id }}"/>
							<input type="hidden" name="early_bird_buyer_id" value="{{ $order->early_bird_buyer->id }}"/>
							<input type="hidden" name="user_id" value="{{ $order->buyer->id }}"/>
							<button class="early_bird_cancel" user_id="{{ $order->buyer->id }}">Cancel</button>
						</form>
					</div>
					<div class="loading" user_id="{{ $order->buyer->id }}"></div>
					<p class="early_bird_error hidden" user_id="{{ $order->buyer->id }}"></p>
				</div>
				@endif
			@endif
			</div>
			<div class="clear">&nbsp;</div>
		    </div>
		@endforeach
	    </div>
	    @endif

        </div>

    </div>

@stop
