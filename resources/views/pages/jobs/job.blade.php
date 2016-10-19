@extends('layouts.main')

@section('title', $job->title)

@section('content')

    <div class="view">
        @if(!Auth::guest() && Auth::user()->user_type != 'employee')
        <div class="alert_window month">
            <div class="alert_window__block">
                <p>{!! $pageTexts[13] !!}</p>
<<<<<<< HEAD
<<<<<<< HEAD
                <h2>{{$job->monthly_salary}} USD</h2>
=======
                <h2>Salary: ${{$job->monthly_salary}} USD/month</h2>
                <p>Monthly price of the purchase: 100 USD</p>
                <p>Markup: 100*0,15 = 15 USD</p>
                <p>Total: 115 USD</p>
=======
                <p>Monthly price of the purchase: {{$job->salary}} USD</p>
                <p>Markup: {{$job->salary}} * 15% fee = {{$job->salary*0.15}} USD</p>
                <p>Total: {{$job->salary + ($job->salary*0.15)}} USD</p>
>>>>>>> 7afcd64... Inform Buyers of Markup Before Purchase
                <div class="cancel"></div>
            </div>
        </div>
        @endif

        <div class="alert_window year">
            <div class="alert_window__block">
                <p>{!! $pageTexts[13] !!}</p>
                <h2>Salary: ${{$job->yearly_salary}} USD</h2>
>>>>>>> fc96e4e... Markup info
                <div class="cancel"></div>
            </div>
        </div>

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

                                <div class="fb"><img src="{{asset('img/Profile/fb.png')}}" alt="alt">facebook.com/{{$employee->first_name.$employee->last_name}}</div>

                                <div class="twitter"><img src="{{asset('img/View/twitter.png')}}" alt="alt">twitter.com/{{$employee->first_name.$employee->last_name}}</div>

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

                                    <span class="amount">
                                        @if(Auth::guest())
                                            ${{$job->salary}}/mo
                                        @elseif(!Auth::guest() && Auth::user()->user_type != 'employee')
                                            ${{$job->monthly_price}}/mo
                                        @else
                                            ${{$job->monthly_salary}}/mo
                                        @endif
                                    </span>

                                </div>

                                <div class="block orange">

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

                            @if (Auth::guest())
                                <a href="/login?fromJob={{$job->id}}"><button>Buy</button></a>
                                @if(!$employee || $employeeStatus['status'] == 'leave')
                                    {{--If job has no employee or employee will leave this job--}}
                                    <button class="apply">Apply for this Job</button>
                                @endif
                            @elseif(Auth::user()->user_type == 'employee')
                                @if($employeeRequest)
                                    @if($employeeRequest->status == 'pending')
                                            <span class="pending">Your request is pending</span>
                                    @elseif($employeeRequest->status == 'approved')
                                        <span class="approved">You got the job!</span>
                                    @elseif($employeeRequest->status == 'rejected')
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
                                @elseif($jobOrdered)
				    <a href="/purchase/{{ $user_order_info->id }}"><span class="approved need">PLEASE COMPLETE PAYMENT</span></a>
                                @else
                                    {{--Это было для сохранения карт и авто-оплат--}}
                                    {{--<a href="/purchase/{{$job->id}}"><button>Buy</button></a>--}}
                                    <form role="form" method="POST" action="{{ url('/order/store') }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="job_id" value="{{$job->id}}">
                                        <button type="submit">Order</button>
                                    </form>
                                @endif
                            @endif

                        </div>

                        @if(!Auth::guest() && Auth::user()->user_type == 'employee')
                            <p class="window"><span>Salary <img src="{{asset('img/View/circle.png')}}" alt="alt"></span></p>
                        @endif
                        <div class="recent">

                            @if(count($orders) > 0)

                                <h2>{!! $pageTexts[12] !!}</h2>

                                <div class="wrapper">

                                @foreach($orders as $order)
                                        <?php
                                            $buyer = $order->buyer()->first();
                                        ?>
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

        </div>

    </div>

@stop
