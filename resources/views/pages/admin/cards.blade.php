@extends('layouts.admin')

@section('title', 'LIST OF CARDS')

@section('content')
    <div class="content_form">
        <div class="list_wrapper">
            @foreach($cards as $card)
            <div class="list_wrapper__item" id="item_{{$card->id}}">
                <div class="apr_rej">
                    <button type="submit" class="approve">Approve</button>
                    <button type="submit" class="reject">Reject</button>
                </div>
                <h2>{{$card->title}}</h2>
                <p>{{$card->description}}</p>
                <div class="item_info">
                    <div class="item_info__numbers">
                        <span class="max">
                            @if($card->sales_count > 0)
                                <a href="/admin/orders/{{$card->id}}">
                            @endif
                                Max clients: {{$card->sales_count}}/{{$card->max_clients_count}}і
                            @if($card->sales_count > 0)
                                </a>
                            @endif
                        </span>
                        <span class="permorm">
                            @if($card->employees_count > 0 ||  $card->employee_requests_count)
                                <a style="{{$card->employee_requests_count > 0 ? 'color: #ff480b;' : ''}}" href="/admin/employee_requests/{{$card->id}}">
                            @endif
                                Performer: {{$card->employees_count}}
                            @if($card->employees_count > 0  ||  $card->employee_requests_count)
                                </a>
                            @endif
                        </span>
                    </div>
                    <span class="title">{{$card->category()->first()->title}}</span>
                    <div class="buttons">
                        <a href="/admin/card/{{$card->id}}/edit"><button><img src="{{asset('img/Admin/edit.png')}}" alt="alt"></button></a>
                        <button class="deleteCardButton" data-card_id = "{{$card->id}}"><img src="{{asset('img/Admin/delete.png')}}" alt="alt"></button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.0.0.js" integrity="sha256-jrPLZ+8vDxt2FnE1zvZXCkCcebI/C8Dt5xyaQBjxQIo=" crossorigin="anonymous"></script>
    <script>
        $('.deleteCardButton').click(function () {
            if(confirm("Are you sure? All data related with this card will be deleted! (Orders, Requests e.t.c...)")){
                var card_id = $(this).attr('data-card_id');
                $.post('http://jobgrouper.com/api/deleteJob/' + card_id, {}, function (data) {
                    switch(data){
                        case 'success':
                            $('#item_' + card_id).remove();
                            break;
                        default:
                            alert('Error has been occurred!');
                    }
                });
            }
        });
    </script>
@stop