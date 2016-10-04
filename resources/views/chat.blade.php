@extends('layouts.main')
@section('content')
    <script src="https://code.jquery.com/jquery-3.0.0.js" integrity="sha256-jrPLZ+8vDxt2FnE1zvZXCkCcebI/C8Dt5xyaQBjxQIo=" crossorigin="anonymous"></script>
    <style>
        .chat {
            display: block;
            width: 500px;
            height: auto;
            background-color: darkorchid;
            padding: 10px;
        }

        .chat .messages {
            display: block;
            width: 100%;
            height: 500px;
            background-color: cornsilk;
            overflow: scroll;
        }

        .chat textarea {
            display: block;
            width: 495px;
            height: 100px;
            resize: none;
        }
        .chat input[type="text"] {
            display: block;
            width: 200px;
            height: 50px;
            left: 50px;
        }

        .chat button {
            display: block;
            float: left;
            width: 200px;
            height: 55px;
        }
    </style>

    <div class="chat">
        <div class="messages"></div>
        <textarea name="" id="message" cols="30" rows="10" placeholder="Message"></textarea>
        <button id="sendMsg">Send message</button>
        <input type="text" id="recipientID" class="" placeholder="Recipient ID">
    </div>

    <script>
        var socket = new WebSocket("ws://jobgrouper.com:8888");
        socket.onopen = function () {
            //alert("Соединение установлено.");

        };

        socket.onclose = function (event) {
            if (event.wasClean) {
                alert('Соединение закрыто чисто');
            } else {
                alert('Обрыв соединения'); // например, "убит" процесс сервера
            }
            alert('Код: ' + event.code + ' причина: ' + event.reason);
        };

        socket.onmessage = function (event) {
            var message = $.parseJSON(event.data);
            var string = '<br><b>'+message.senderName+'</b>  ' + message.datet + '<br>' + message.text + '<br>';
            $('.messages').append(string);

        };

        socket.onerror = function (error) {
            alert("Ошибка " + error.message);
        };

        $('#sendMsg').click(function () {
            var message = new Object();
            message.recipientID = $('#recipientID').val();
            message.text = $('#message').val();
            if(message.text != ''){
                socket.send(JSON.stringify(message));
                $('#message').val('');
            }
        });
    </script>
@endsection
