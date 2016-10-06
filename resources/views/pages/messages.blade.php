@extends('layouts.main')

@section('title', 'Dialogs')

@section('content')




    <div class="message">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <div class="message_title">

                        Messaging system

                    </div>

                    <div class="message_scroll">

                        @if(count($dialogs) > 0)

                            @foreach($dialogs as $dialog)

                                {{$class = ''}}

                                @if($recipientID == $dialog['userID'])

                                    <?php $class = 'active'?>

                                @endif

                                <div class="dialog item clearfix {{$class}}" data-recipientID="{{$dialog['userID']}}">

                                    <div class="img_wrapper">

                                        <img src="{{$dialog['image_url']}}" alt="alt">

                                    </div>

                                    <div class="name">

                                        <h2>{{$dialog['userName']}}</h2>

                                        <p>
                                            <span id="last_message_{{$dialog['userID']}}"><b>{{$dialog['lastSender']}}</b> {{$dialog['lastMessage']}}</span>
                                        </p>

                                    </div>

                                </div>

                            @endforeach

                        @else

                            No dialogs

                        @endif


                    </div>

                    @if(!$recipientID)
                    <p class="choose" id="choose">Please chose the person you want to chat with</p>
                    @endif
                    <div class="message_chat">

                        {{--Chat will be here--}}

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script src="https://code.jquery.com/jquery-3.0.0.js" integrity="sha256-jrPLZ+8vDxt2FnE1zvZXCkCcebI/C8Dt5xyaQBjxQIo=" crossorigin="anonymous"></script>
    <script>


        //Setting sound for input messages

        var audio = {};

        audio["message"] = new Audio();

        audio["message"].src = "http://jobgrouper.com/audio/message.mp3";


        var currentRecipientID = <?=$recipientID?>;         //Current recipient id

        var userPhotoUrl = '<?=$userData['imageUrl']?>';    //Photo of current user

        var recipientPhotoUrl = '';                         //Here will be photo of selected recipient


        //If it is recipient id in url, open dialog with he

        if (currentRecipientID > 0) {

            setChatHistory(currentRecipientID);

        }


        //Selecting dialog

        $('.dialog').click(function () {

            //Add "active" class to selected dialog

            $('.dialog').removeClass('active');

            $(this).addClass('active');


            //Get selected recipient ID

            currentRecipientID = $(this).attr("data-recipientID");


            //Load chat history

            setChatHistory(currentRecipientID);

        });


        var socket = new WebSocket("ws://jobgrouper.com:8888");


        socket.onopen = function () {

            //alert("Соединение установлено.");


        };


        socket.onclose = function (event) {

            if (event.wasClean) {

                //alert('Соединение закрыто чисто');

            } else {
                setChatUnavailableWindow();

                //alert('Обрыв соединения'); // например, "убит" процесс сервера

            }

            // alert('Код: ' + event.code + ' причина: ' + event.reason);

            //setChatUnavailableWindow();

        };


        socket.onmessage = function (event) {
            var message = $.parseJSON(event.data);      //Getting message data

            switch (message.type) {

                case 'message':

                    var messageUserPhotoUrl = '';               //Recipient photo url will be here

                    if (currentRecipientID == message.senderID) {
                        audio["message"].play();
                        messageUserPhotoUrl = recipientPhotoUrl;
                    }
                    else {
                        messageUserPhotoUrl = userPhotoUrl;
                    }

                    if (currentRecipientID == message.senderID || message.senderID == <?=$userData['id']?>) {

                        var string = '<div class="sms">\
                            <div class="head">\
                                <div class="img_wrapper">\
                                    <a href="/account/' + message.senderID + '"><img src="' + messageUserPhotoUrl + '" alt="alt"></a>\
                                </div>\
                                <div class="name"><a href="/account/' + message.senderID + '">' + message.senderName + '</a></div>\
                                <span>' + message.datet + '</span>\
                           </div>\
                            <p class="'+(currentRecipientID == message.senderID ? 'unread' : '')+'">' + message.text + '</p>\
                        </div>';
                        $('.all_sms').append(string);
                    }

                    //Добавление текста сообщения в окно диалога слева
                    $('#last_message_' + message.senderID).html(message.text);


                    //Перемотка чата вниз
                    var d = $('.message_chat .all_sms');
                    d.scrollTop(d.prop("scrollHeight"));


                    //Если пришло сообщение, и textarea focused отмечаем его сразу как прочитаное
                    if($("#message").is(":focus")){
                        markMessageasAsRead();
                    }
                    if(message.senderID != <?=Auth::user()->id?>)
                    {
                        if(message.countNewMessages == 0){
                            message.countNewMessages = '';
                        }
                        $('#newMessagesCount').html(message.countNewMessages);
                    }
                    break;

                case 'typing':

                    /*$('#typing').html('typing...');

                     window.setTimeout(function () {

                     $('#typing').html('');

                     }, 2000);*/

                    break;

            }
        };


        socket.onerror = function (error) {

            //alert("Ошибка " + error.message);

        };


        function sendMessage() {

            var message = new Object();

            message.type = 'message';

            message.recipientID = currentRecipientID;

            message.text = $('#message').val();
            message.sendToAllBuyers = false;
            @if($userData->user_type == 'employee')
                message.sendToAllBuyers = document.getElementById('send_all').checked;
            @endif

            if (message.text != '') {

                socket.send(JSON.stringify(message));

                $('#message').val('');

                if (message.text.length > 25) {

                    message.text = message.text.slice(0, 25);

                    message.text = message.text + '...';

                }

                $('#last_message_' + currentRecipientID).html('<b>You: </b> ' + message.text);
                //--
                var d = $('.message_chat .all_sms');
                d.scrollTop(d.prop("scrollHeight"));

            }

        }


        function sendTyping() {

            var message = new Object();

            message.type = 'typing';

            message.recipientID = currentRecipientID;

            socket.send(JSON.stringify(message));

        }


        function setChatWindow(recipient_id, recipientImgUrl, recipientName) {
            $('#choose').remove();
            window.history.pushState("object or string", "Title", "/messages/" + currentRecipientID);

            var string = '<div class="person">\
                                    <div class="img_wrapper">\
                                        <a href="/account/' + recipient_id + '" target="_blank"><img src="' + recipientImgUrl + '" alt="alt"></a>\
                                    </div>\
                                    <div class="name"><a href="/account/' + recipient_id + '" target="_blank">' + recipientName + '</a></div>\
                            </div>\
                            <div class="all_sms">\
                            </div>\
                            <textarea class="mymsg" id="message" placeholder="Text..."></textarea>\
                            @if($userData->user_type == 'employee')
                            <input type="checkbox" id="send_all">\
                            <label class="hot_label" for="send_all">Send to all buyers in my job card</label>\
                            @endif
                            <button class="send" id="sendMsg">Send</button>';
            $('.message_chat').html(string);
        }


        function setChatUnavailableWindow() {

            var string = '<div class="person">\
                                   <div class="name">Sorry, message service is temporarily unavailable</div>\
                            </div>';
            $('.message_chat').html(string);

        }


        function setChatHistory(recipientID) {

            $.get('http://jobgrouper.com/api/messages_history/' + recipientID, {}, function (data) {

                console.log(data);

                recipientPhotoUrl = data.image_url;

                setChatWindow(recipientID, data.image_url, data.recipientName);

                for (var i = 0; i < data.messages.length; i++) {

                    var messageUserPhotoUrl = '';                           //Recipient photo url will be here

                    if (currentRecipientID == data.messages[i].sender_id) {

                        messageUserPhotoUrl = recipientPhotoUrl;

                    }

                    else {

                        messageUserPhotoUrl = userPhotoUrl;

                    }

                    var string = '<div class="sms">\
                            <div class="head">\
                                <div class="img_wrapper">\
                                    <a href="/account/' + data.messages[i].sender_id + '" target="_blank"><img src="' + messageUserPhotoUrl + '" alt="alt"></a>\
                                </div>\
                                <div class="name"><a href="/account/' + data.messages[i].sender_id + '" target="_blank">' + data.messages[i].senderName + '</a></div>\
                                <span>' + data.messages[i].created_at  +'</span>\
                           </div>\
                            <p class="'+(data.messages[i].new ? 'unread' : '')+'">' + data.messages[i].message + '</p>\
                        </div>';
                    $('.all_sms').append(string);

                }
                var d = $('.message_chat .all_sms');
                d.scrollTop(d.prop("scrollHeight"));

            });

            markMessageasAsRead();
        }

        function markMessageasAsRead(){
            $.post('http://jobgrouper.com/api/markMessageasAsRead/' + currentRecipientID, {}, function (data) {
                $('.unread').removeClass('unread');
                if(data == 0){
                    data = '';
                }
                
                $('#newMessagesCount').html(data);
            });
        }

        $('body').delegate('#message', 'keypress', function (e) {
            sendTyping();
            if ((e.keyCode == 10 || e.keyCode == 13) && e.ctrlKey) {
                this.value = this.value.substring(0, this.selectionStart) + "\n";
            }

            if (e.which == 13) {
                event.preventDefault();
                sendMessage();
            }
        });


        $('body').delegate('#message', 'click', markMessageasAsRead);
        $('.message_chat').delegate('#sendMsg', 'click', sendMessage);
        $('#sendMsg').click(sendMessage);

    </script>

@endsection

