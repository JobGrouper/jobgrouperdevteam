@extends('layouts.admin')



@section('title', 'LIST OF EMPLOYEE REQUESTS')



@section('content')

    <div class="content_form">

        <div class="userslist_wrapper">

            @foreach($employeeRequests as $employeeRequest)

                <?php
                    $employee = $employeeRequest->employee()->first();
                    $job = $employeeRequest->job()->get()->first();

                ?>

                <div class="userslist_wrapper__item">

                    <div class="img_wrapper"><a href="/account/{{$employee->id}}" target="_blank"><img src="{{$employee->image_url}}" alt="alt"></a></div>

                    <span class="name">{{$employee->fullname}}</span>

                    <button class="type {{($employeeRequest->status == 'pending' ? 'seller' : '')}}">{{$employeeRequest->status}}</button>

                    <div class="regdate">

                        <div class="regdate_date">{{$employeeRequest->formated_created_at}}</div>

                        <div class="regdate_text">requested</div>
                        @if($job->employee_status['status'] == 'leave' && $employeeRequest->employee_id == $job->employee_id)
                            <div class="regdate_text">will leave this job {{$employeeRequest->job()->get()->first()->employee_status['leave_date']}}</div>
                        @endif

                        @if($job->employee_status['status'] == 'leave' && $employeeRequest->employee_id == $job->potential_employee_id)
                            <div class="regdate_text">Potential employee</div>
                        @endif
                    </div>
                    <div class="buttons">
                    <!-- <button id="button_{{$employee->id}}" class="floatbtn deactivateUserButton" data-user_id = "{{$employee->id}}"><img  src="{{asset(($employee->active ? 'img/Admin/Lockgray.png' : 'img/Admin/Lock.png'))}}" alt="alt"></button> -->
                    @if($employeeRequest->status == 'pending' || $employeeRequest->status == 'rejected')

                            <form role="form" method="POST" action="{{ url('/employee_request/approve') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="employee_request_id" value="{{$employeeRequest->id}}">
                                <button type="submit" class="approve">Approve</button>
                            </form>
                    @endif
                    @if($employeeRequest->status == 'pending' || $employeeRequest->status == 'approved')
                            <form role="form" method="POST" action="{{ url('/employee_request/reject') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="employee_request_id" value="{{$employeeRequest->id}}">
                                <button type="submit" class="reject">Reject</button>
                            </form>

                    @endif
                    </div>
                </div>

            @endforeach

        </div>

    </div>



    {{--<script src="https://code.jquery.com/jquery-3.0.0.js" integrity="sha256-jrPLZ+8vDxt2FnE1zvZXCkCcebI/C8Dt5xyaQBjxQIo=" crossorigin="anonymous"></script>--}}

    {{--<script>--}}

        {{--$('.deactivateUserButton').click(function () {--}}

            {{--var user_id = $(this).attr('data-user_id');--}}

            {{--$.post('http://jobgrouper.com/api/deactivateUser/' + user_id, {}, function (data) {--}}

                {{--switch(data){--}}

                    {{--case 'activated':--}}

                            {{--$('#button_' + user_id).html('<img  src="{{asset('img/Admin/Lockgray.png')}}" alt="alt">');--}}

                        {{--break;--}}

                    {{--case 'deactivated':--}}

                            {{--$('#button_' + user_id).html('<img  src="{{asset('img/Admin/Lock.png')}}" alt="alt">');--}}

                        {{--break;--}}

                    {{--default:--}}

                        {{--alert('Error has been occurred!');--}}

                {{--}--}}

            {{--});--}}

        {{--});--}}

    {{--</script>--}}

@stop