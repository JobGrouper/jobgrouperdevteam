@extends('layouts.main')

@section('title', 'My Jobs')

@section('content')

<div class="myjobs">
    <div class="container">
        <div class="row">

            <p class="title">My Jobs</p>
            @if($jobs->count())
                @foreach($jobs as $job)
                    <div class="job_item">
                        <h1 class="active">
                            <span class="job_item__title">
                                <a  href="/job/{{$job->id}}">{{$job->title}}</a>
                                {!!  ($job->status == 'working' ? '<span class="green">Active</span>' : '<span class="red">Waiting for buyers</span>')  !!}
                            </span>
                            <p class="myjobs_text">{{$job->description}}</p>
                            @if($job->employee_status['status'] == 'leave')
                                <span class="sent" style="display: block;">Leave request sent</span>
                            @else
                                <button job-id="{{$job->id}}" class="request_close leave_api">Leave job request
                                </button>
                                <span class="sent before">Leave request sent</span>
                            @endif
                        </h1>
                        <div class="jobs_acc">
                            <div class="workers">
                                @if(count($job->sales()->get()) > 0)
                                    @foreach($job->sales()->where('status', 'in_progress')->orWhere('status', '=', 'pending')->get() as $order)
                                        <?php
                                            $buyer = $order->buyer()->first();
                                            $closeRequest = $order->close_order_requests()->where('originator_id', '=', $employee->id)->first();
                                        ?>
                                        @if($buyer)
                                            <div class="workers_item clearfix" data-id="{{$order->id}}">
                                                <div class="img_wrapper">
                                                    <img src="{{ asset($buyer->image_url) }}" alt="alt">
                                                </div>
                                                <div class="rating">
                                                    <div class="stars">
                                                        <div class="yellow"></div>
                                                    </div>
                                                    <div class="rating_name">{{$buyer->fullname}}</div>
                                                    <a href="/messages/{{$buyer->id}}">message
                                                        ({{$employee->getNewMessages($buyer->id)}} new)</a>
                                                </div>
                                                <div class="salary">
                                                    <p>${{$job->salary}}/month</p>
                                                    <p class="month">work {{$order->created_at->diffInMonths() + 1}}
                                                        month{{(($order->created_at->diffInMonths() + 1) > 1 ? 's' : '') }}</p>
                                                </div>
                                                @if($order->status == 'closed')
                                                    <button class="Request">Order closed</button>
                                                @else
                                                    <button class="request_buyer_removal cancelbtn"
                                                            data-order_id="{{$order->id}}">Report Buyer
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    No orders yet
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                You don't have any existing jobs, but you can apply to jobs <a href="jobs">here</a>
            @endif

            @if($potentialJobs->count())
                <p class="potent">Potential jobs</p>
                @foreach($potentialJobs as $job)
                    <div class="job_item">
                        <h1 class="active">
                            <span class="job_item__title">{{$job->title}}
                                <span class="potent_span">Potential job</span>
                            </span>
                            <p class="myjobs_text">{{$job->description}}</p>
                            {{--@if($job->employee_status['status'] == 'leave')--}}
                            {{--<span class="sent" style="display: block;">Leave request sent</span>--}}
                            {{--@else--}}
                            {{--<button job-id="{{$job->id}}" class="request_close leave_api">Leave job request</button>--}}
                            {{--<span class="sent before">Leave request sent</span>--}}
                            {{--@endif--}}
                        </h1>
                        <div class="jobs_acc">
                            <div class="workers">
                                @if(count($job->sales()->get()) > 0)
                                    @foreach($job->sales()->whereIn('status', ['in_progress', 'pending'])->get() as $order)
                                        <?php
                                            $buyer = $order->buyer()->first();
                                            $closeRequest = $order->close_order_requests()->where('originator_id', '=', $employee->id)->first();
                                        ?>
                                        <div class="workers_item clearfix" data-id="{{$order->id}}">
                                            <div class="img_wrapper">
                                                <img src="{{ asset($buyer->image_url) }}" alt="alt">
                                            </div>
                                            <div class="rating">
                                                <div class="stars">
                                                    <div class="yellow"></div>
                                                </div>
                                                <div class="rating_name">{{$buyer->fullname}}</div>
                                                <a href="/messages/{{$buyer->id}}">message
                                                    ({{$employee->getNewMessages($buyer->id)}} new)</a>
                                            </div>

                                            <div class="salary">
                                                <p>${{$job->salary}}/month</p>
                                                <p class="month">work {{$order->created_at->diffInMonths() + 1}}
                                                    month{{(($order->created_at->diffInMonths() + 1) > 1 ? 's' : '') }}</p>
                                            </div>
                                            @if($order->status == 'closed')
                                                <button class="Request">Order closed</button>
                                            @else
                                                <button class="request_buyer_removal cancelbtn"
                                                        data-order_id="{{$order->id}}">Report Buyer
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    No orders yet
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <p class="potent">Jobs awaiting activation</p>
            @if(count($jobsAwaitingActivation) > 0)
                @foreach($jobsAwaitingActivation as $jobAwaitingActivation)
                    <div class="job_item">
                        <h1 class="active">
                            <span class="job_item__title"><a href="/job/{{$jobAwaitingActivation->id}}">{{$jobAwaitingActivation->title}}</a></span>
                            <p class="myjobs_text">{{$jobAwaitingActivation->description}}</p>
                        </h1>
                        <div class="loading" job_id="{{$jobAwaitingActivation->id}}"></div>
                        <div class="job_button_error center" job_id="{{$jobAwaitingActivation->id}}"></div>
                        <button job-id="{{$jobAwaitingActivation->id}}" class="request_close leave_api">Leave job
                        </button>

                        <div class="jobs_acc">

                        </div>
                    </div>
                @endforeach
            @else
                <p>No jobs listed</p>
            @endif


            <p class="potent">Jobs with pending applications</p>
            @if($employeeRequests->count())
                @foreach($employeeRequests as $employeeRequest)
                    <div class="job_item">
                        <h1 class="active">
                            <span class="job_item__title"><a href="/job/{{$employeeRequest->job()->first()->id}}">{{$employeeRequest->job()->first()->title}}</a></span>
                            <p class="myjobs_text">{{$employeeRequest->job()->first()->description}}</p>
                            <button request-id="{{$employeeRequest->id}}" class="request_close close_api">Cancel
                            </button>
                        </h1>

                        <div class="jobs_acc">

                        </div>
                    </div>
                @endforeach
            @else
                <p>No jobs listed</p>
            @endif
        </div>
    </div>
</div>
@endsection
