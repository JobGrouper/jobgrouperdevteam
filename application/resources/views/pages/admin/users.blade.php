@extends('layouts.admin')

@section('title', 'LIST OF USERS')

@section('content')
    <div class="content_form">
        <div class="userslist_wrapper">
            @foreach($users as $user)
                <div class="userslist_wrapper__item">
                    <div class="img_wrapper"><a href="/account/{{$user->id}}" target="_blank"><img src="{{$user->image_url}}" alt="alt"></a></div>
                    <span class="name">{{$user->fullname}}</span>
                    <button class="type {{($user->user_type == 'buyer' ? '' : 'seller')}}">{{($user->user_type == 'buyer' ? 'buyer' : 'seller')}}</button>
                    <div class="regdate">
                        <div class="regdate_date">{{$user->registrate_at}}</div>
                        <div class="regdate_text">registration</div>
                    </div>
                    @if($user->role != 'admin')
                        <button id="button_{{$user->id}}" class="floatbtn deactivateUserButton" data-user_id = "{{$user->id}}"><img  src="{{asset(($user->active ? 'img/Admin/Lockgray.png' : 'img/Admin/Lock.png'))}}" alt="alt"></button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.0.0.js" integrity="sha256-jrPLZ+8vDxt2FnE1zvZXCkCcebI/C8Dt5xyaQBjxQIo=" crossorigin="anonymous"></script>
    <script>
        $('.deactivateUserButton').click(function () {
            var user_id = $(this).attr('data-user_id');
            $.post('http://jobgrouper.com/api/deactivateUser/' + user_id, {}, function (data) {
                switch(data){
                    case 'activated':
                            $('#button_' + user_id).html('<img  src="{{asset('img/Admin/Lockgray.png')}}" alt="alt">');
                        break;
                    case 'deactivated':
                            $('#button_' + user_id).html('<img  src="{{asset('img/Admin/Lock.png')}}" alt="alt">');
                        break;
                    default:
                        alert('Error has been occurred!');
                }
            });
        });
    </script>
@stop