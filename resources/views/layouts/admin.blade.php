<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>@yield('title')</title>

    <script type="text/javascript">
        var jg_domain = "{{ Request::root() }}";
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,800italic,800,700italic,700,600italic,400italic,600,300,300italic' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="{{ asset('libs/bootstrap/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/font-awesome-4.2.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/magnific/magnific-popup.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/Cropper/assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/Cropper/dist/cropper.css') }}">

    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">

    <link rel="stylesheet" href="{{ asset('css/admin/adminmedia.css') }}">

    <script type="text/javascript" src="{{ asset('js/utilities.js') }}"></script>

    @yield('autoload_scripts')

</head>

<body>

<div class="addcard_wrapper">
    
    <div id="small-dialog3" class="zoom-anim-dialog mfp-hide">
        <h1></h1>
        <select id="category"  name="category_id">

            @foreach($categories as $category)

                <option value="{{$category->id}}">{{$category->title}}</option>

            @endforeach


        </select>
        <div class="buttons">
            <button class="buttons_ok">Ok</button>
            <button class="buttons_cancel">Cancel</button>
        </div>
    </div>

    <div class="sidebar">

        <div class="sidebar_logo">

            <a href="/"><img src="{{ asset('img/logo2.png') }}" alt="alt"></a>

        </div>

        <div class="sidebar_content">

            <div class="sidebar_logo2">

                <a href="/"><img src="{{ asset('img/logo2.png') }}" alt="alt"></a>

            </div>

            <a href="/admin/card"><button class="create"><img src="{{ asset('img/Admin/Ribbon.png') }}" alt="alt">Create Card</button></a>

            <ul>

                <li class="{{(Route::current()->getName() == 'users' ? 'active' : '')}}" ><a href="/admin/users"><img src="{{ asset('img/Admin/Profile.png') }}" alt="alt"><span>list of users</span></a></li>

                <li class="{{(Route::current()->getName() == 'cards' ? 'active' : '')}}" ><a href="/admin/cards"><img src="{{ asset('img/Admin/Notepad.png') }}" alt="alt"><span>list of cards</span></a></li>

                <li class="{{(Route::current()->getName() == 'texts' ? 'active' : '')}}"><a href="/admin/texts"><img src="{{ asset('img/Admin/New_Post.png') }}" alt="alt"><span>text of website</span></a></li>

                <li class="{{(Route::current()->getName() == 'categories' ? 'active' : '')}}"><a href="/admin/categories"><img src="{{ asset('img/Admin/Stats_Alt.png') }}" alt="alt"><span>Categories</span></a></li>


            </ul>

        </div>

    </div>

    <div class="content">

        <div class="content_header">

            <h1><button class="ham"><img src="{{ asset('img/Category/ham.png') }}" alt="alt"></button>@yield('title')</h1>

            <div class="user">

                <div class="img_wrapper"><img src="{{ asset('img/Admin/ellipse.png') }}" alt="alt"></div>

                <div>

                    <h2>{{$userData->full_name}}</h2>

                    <a href="/logout">Logout</a>

                </div>

            </div>

        </div>

        @yield('content')

    </div>

    {{--<div class="content">--}}

        {{--<div class="content_header">--}}

            {{--<h1><button class="ham"><img src="{{ asset('img/Category/ham.png') }}" alt="alt"></button>List of cards</h1>--}}

            {{--<div class="user">--}}

                {{--<div class="img_wrapper"><img src="{{ asset('img/Admin/ellipse.png') }}" alt="alt"></div>--}}

                {{--<div>--}}

                    {{--<h2>Robert Paters</h2>--}}

                    {{--<a href="#">Logout</a>--}}

                {{--</div>--}}

            {{--</div>--}}

        {{--</div>--}}

        {{--<div class="content_form">--}}

            {{--<div class="list_wrapper">--}}

                {{--<div class="list_wrapper__item">--}}

                    {{--<h2>Rockstar Developer!</h2>--}}

                    {{--<p>Lorem ipsum dolor sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. Est eu pertinaciaen delacrue instructiol vel eu natum vedi idqran ende salutandi no per. Ipsum dolor lorem sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. </p>--}}

                    {{--<div class="item_info">--}}

                        {{--<div class="item_info__numbers">--}}

                            {{--<span class="max">Max clients: 15/7</span>--}}

                            {{--<span class="permorm">Performer: 1</span>--}}

                        {{--</div>--}}

                        {{--<span class="title">Category:  Web design</span>--}}

                        {{--<div class="buttons">--}}

                            {{--<button><img src="img/Admin/edit.png" alt="alt"></button>--}}

                            {{--<button><img src="img/Admin/delete.png" alt="alt"></button>--}}

                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

                {{--<div class="list_wrapper__item">--}}

                    {{--<h2>Rockstar Developer!</h2>--}}

                    {{--<p>Lorem ipsum dolor sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. Est eu pertinaciaen delacrue instructiol vel eu natum vedi idqran ende salutandi no per. Ipsum dolor lorem sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. </p>--}}

                    {{--<div class="item_info">--}}

                        {{--<div class="item_info__numbers">--}}

                            {{--<span class="max">Max clients: 15/7</span>--}}

                            {{--<span class="permorm">Performer: 1</span>--}}

                        {{--</div>--}}

                        {{--<span class="title">Category:  Web design</span>--}}

                        {{--<div class="buttons">--}}

                            {{--<button><img src="img/Admin/edit.png" alt="alt"></button>--}}

                            {{--<button><img src="img/Admin/delete.png" alt="alt"></button>--}}

                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

                {{--<div class="list_wrapper__item">--}}

                    {{--<h2>Rockstar Developer!</h2>--}}

                    {{--<p>Lorem ipsum dolor sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. Est eu pertinaciaen delacrue instructiol vel eu natum vedi idqran ende salutandi no per. Ipsum dolor lorem sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. </p>--}}

                    {{--<div class="item_info">--}}

                        {{--<div class="item_info__numbers">--}}

                            {{--<span class="max">Max clients: 15/7</span>--}}

                            {{--<span class="permorm">Performer: 1</span>--}}

                        {{--</div>--}}

                        {{--<span class="title">Category:  Web design</span>--}}

                        {{--<div class="buttons">--}}

                            {{--<button><img src="img/Admin/edit.png" alt="alt"></button>--}}

                            {{--<button><img src="img/Admin/delete.png" alt="alt"></button>--}}

                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

                {{--<div class="list_wrapper__item">--}}

                    {{--<h2>Rockstar Developer!</h2>--}}

                    {{--<p>Lorem ipsum dolor sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. Est eu pertinaciaen delacrue instructiol vel eu natum vedi idqran ende salutandi no per. Ipsum dolor lorem sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. </p>--}}

                    {{--<div class="item_info">--}}

                        {{--<div class="item_info__numbers">--}}

                            {{--<span class="max">Max clients: 15/7</span>--}}

                            {{--<span class="permorm">Performer: 1</span>--}}

                        {{--</div>--}}

                        {{--<span class="title">Category:  Web design</span>--}}

                        {{--<div class="buttons">--}}

                            {{--<button><img src="img/Admin/edit.png" alt="alt"></button>--}}

                            {{--<button><img src="img/Admin/delete.png" alt="alt"></button>--}}

                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

                {{--<div class="list_wrapper__item">--}}

                    {{--<h2>Rockstar Developer!</h2>--}}

                    {{--<p>Lorem ipsum dolor sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. Est eu pertinaciaen delacrue instructiol vel eu natum vedi idqran ende salutandi no per. Ipsum dolor lorem sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. </p>--}}

                    {{--<div class="item_info">--}}

                        {{--<div class="item_info__numbers">--}}

                            {{--<span class="max">Max clients: 15/7</span>--}}

                            {{--<span class="permorm">Performer: 1</span>--}}

                        {{--</div>--}}

                        {{--<span class="title">Category:  Web design</span>--}}

                        {{--<div class="buttons">--}}

                            {{--<button><img src="img/Admin/edit.png" alt="alt"></button>--}}

                            {{--<button><img src="img/Admin/delete.png" alt="alt"></button>--}}

                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

                {{--<div class="list_wrapper__item">--}}

                    {{--<h2>Rockstar Developer!</h2>--}}

                    {{--<p>Lorem ipsum dolor sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. Est eu pertinaciaen delacrue instructiol vel eu natum vedi idqran ende salutandi no per. Ipsum dolor lorem sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. </p>--}}

                    {{--<div class="item_info">--}}

                        {{--<div class="item_info__numbers">--}}

                            {{--<span class="max">Max clients: 15/7</span>--}}

                            {{--<span class="permorm">Performer: 1</span>--}}

                        {{--</div>--}}

                        {{--<span class="title">Category:  Web design</span>--}}

                        {{--<div class="buttons">--}}

                            {{--<button><img src="img/Admin/edit.png" alt="alt"></button>--}}

                            {{--<button><img src="img/Admin/delete.png" alt="alt"></button>--}}

                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

                {{--<div class="list_wrapper__item">--}}

                    {{--<h2>Rockstar Developer!</h2>--}}

                    {{--<p>Lorem ipsum dolor sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. Est eu pertinaciaen delacrue instructiol vel eu natum vedi idqran ende salutandi no per. Ipsum dolor lorem sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. </p>--}}

                    {{--<div class="item_info">--}}

                        {{--<div class="item_info__numbers">--}}

                            {{--<span class="max">Max clients: 15/7</span>--}}

                            {{--<span class="permorm">Performer: 1</span>--}}

                        {{--</div>--}}

                        {{--<span class="title">Category:  Web design</span>--}}

                        {{--<div class="buttons">--}}

                            {{--<button><img src="img/Admin/edit.png" alt="alt"></button>--}}

                            {{--<button><img src="img/Admin/delete.png" alt="alt"></button>--}}

                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

                {{--<div class="list_wrapper__item">--}}

                    {{--<h2>Rockstar Developer!</h2>--}}

                    {{--<p>Lorem ipsum dolor sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. Est eu pertinaciaen delacrue instructiol vel eu natum vedi idqran ende salutandi no per. Ipsum dolor lorem sit amet, ea sit cetero assusamus, a idqran ende salutandi no per. </p>--}}

                    {{--<div class="item_info">--}}

                        {{--<div class="item_info__numbers">--}}

                            {{--<span class="max">Max clients: 15/7</span>--}}

                            {{--<span class="permorm">Performer: 1</span>--}}

                        {{--</div>--}}

                        {{--<span class="title">Category:  Web design</span>--}}

                        {{--<div class="buttons">--}}

                            {{--<button><img src="img/Admin/edit.png" alt="alt"></button>--}}

                            {{--<button><img src="img/Admin/delete.png" alt="alt"></button>--}}

                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

            {{--</div>--}}

        {{--</div>--}}

    {{--</div>--}}

</div>



<script src="{{ asset('js/libs.min.js') }}"></script>

<script src="{{ asset('libs/magnific/jquery.magnific-popup.min.js') }}"></script>

<script src="{{ asset('libs/Cropper/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('libs/Cropper/dist/cropper.js') }}"></script>

<script src="{{ asset('js/admin/admin.js') }}"></script>

</body>

</html>
