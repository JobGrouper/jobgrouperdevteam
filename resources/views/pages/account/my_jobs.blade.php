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
                    </div>
                @endforeach
            @else
                <p>No jobs listed</p>
            @endif
        </div>
    </div>
</div>
@endsection
