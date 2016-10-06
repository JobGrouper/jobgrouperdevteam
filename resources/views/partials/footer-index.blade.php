<footer class="main_footer">
    <div class="newmess"><span></span><div class="cancel"></div></div>

    <div class="container">

        <div class="row">

            <div class="col-md-12">


                <div class="content">
                    <span class="copy">{!! $pageTexts[8] !!} | {!! $pageTexts[9] !!} | {!! $pageTexts[10] !!}</span>
                </div>

                <span class="links"><a href="/help">Help</a>|<a href="/terms">Terms &amp; Conditions</a></span>
                

                <div class="main_footer__social">

                    <ul>

                        <li><a href="https://uk-ua.facebook.com/" target="_blank"><i class="fa fa-facebook"></i></a></li>

                        <li><a href="https://twitter.com/" target="_blank"><i class="fa fa-twitter"></i></a></li>

                        <li><a href="https://plus.google.com/" target="_blank"><i class="fa fa-google-plus"></i></a></li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</footer>




<script src="{{ asset('js/libs.min.js') }}"></script>

<script src="{{ asset('libs/magnific/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('libs/pickmeup/jquery.pickmeup.min.js') }}"></script>
<script src="{{ asset('libs/Cropper/assets/js/bootstrap.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
<script src="{{ asset('libs/Cropper/dist/cropper.js') }}"></script>

<script src="{{ asset('js/common.js') }}"></script>
@if(Route::current()->getName() != 'messages')
    @if (!Auth::guest())
    <script>
        //Setting sound for input messages
        var audio = {};
        audio["message"] = new Audio();
        audio["message"].src = "http://jobgrouper.com/audio/message.mp3";
        var socket = new WebSocket("ws://jobgrouper.com:8888");
        socket.onopen = function () {
            //alert("Соединение установлено.");
        };

        socket.onclose = function (event) {
            if (event.wasClean) {
                //
            } else {
                //
            }
            //
        };

        socket.onmessage = function (event) {
            var message = $.parseJSON(event.data);      //Getting message data
            switch (message.type){
                case 'message':
                    //console.log(message);
                    if(message.countNewMessages == 0){
                        message.countNewMessages = '';
                    }
                    $('#newMessagesCount').html(message.countNewMessages);
                    $(".main_footer .newmess").show();
                    $(".main_footer .newmess").click(function() {
                       window.location.href = "/messages/"+message.senderID;
                    });
                    $(".main_footer .newmess .cancel").click(function() {
                        $(this).parent().fadeOut("fast");
                    });
                    $(".main_footer .newmess").find("span").html('<b>'+message.senderName+'</b> sent you a message.');
                    audio["message"].play();


                    break;
            }
        };

        socket.onerror = function (error) {
            //
        };
    </script>
    @endif
@endif
