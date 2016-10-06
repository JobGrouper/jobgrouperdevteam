
<div id="small-dialog4" class="zoom-anim-dialog mfp-hide">
    <div class="header">
        <h3>Write a review</h3>
        <p class="name">SuperStart Logo Designer</p>
        <div class="stars">
            <div class="wrapper clearfix">
                <div class="star"></div>
                <div class="star"></div>
                <div class="star"></div>
                <div class="star"></div>
                <div class="star"></div>
            </div>
        <div class="yellow"></div>
        </div>
    </div>
    <p class="recall">Recall</p>
    <div class="text">
        <textarea></textarea>
    </div>
    <button class="send">Send</button>
</div>
<header class="dark_header">

    <div class="dark_header__line">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <button class="ham"><img src="{{ asset('img/Category/ham.png') }}" alt="alt"></button>

                    <a class="smalllogo" href="/"><img src="{{ asset('img/logo2.png') }}" alt="alt"></a>

                    <div class="name">
                        @if (Auth::guest())

                            <a href="/register">Sign Up</a>

                            <a href="/login">Log In</a>

                        @else

                            <a class="quest" href="/help"><img src="{{ asset('img/Myjobs/circled-help.png')}}" alt="alt"></a>

                            <a class="mail" href="/messages"><img src="{{ asset('img/Myjobs/mail.png')}}" alt="alt"> <span id="newMessagesCount">{{$userData->getNewMessages()}}</span></a>

                            <span>
                                {{$userData->fullName}}<img src="{{ asset('img/Category/arrow.png')}}" alt="alt">
                                <div class="name_hover">
                                    <ul>
                                        <li><a href="/account">Profile</a></li>
                                        @if($userData->user_type == 'employee')
                                            <li><a href="/my_jobs">My Jobs</a></li>
                                        @else
                                            <li><a href="/my_orders">My Orders</a></li>
                                            <li><a href="/my_transactions">Transactions</a></li>
                                        @endif
                                        @if($userData->role == 'admin')
                                            <li><a href="/admin">Admin Panel</a></li>
                                        @endif
                                        <li><a href="/logout">Log Out</a></li>
                                    </ul>
                                </div>
                            </span>



                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="dark_header__categories">

        <div class="container">

            <div class="row">
                <div class="col-md-3">
                    <?php
                    $i = 1;
                    $itemInCol = ceil($categories->count() / 4);
                    ?>
                    @foreach($categories as $category)

                        @if($category->title  == 'All Categories')
                            <a href="/jobs">All Categories</a>
                        @else
                            <a href="/jobs/category/{{$category->id}}">{{$category->title}}</a>
                        @endif

                        @if($i % 2 == 0)
                            </div>
                            <div class="col-md-3">
                        @endif

                    <?php
                    ++$i;
                    ?>
                    @endforeach
                </div>
            </div>
            @if(!Auth::guest())
            <div class="mobile_dark">
                <a class="quest" href="/help"><img src="{{ asset('img/Myjobs/circled-help.png')}}" alt="alt"></a>
                    <a class="mail" href="/messages"><img src="{{ asset('img/Myjobs/mail.png')}}" alt="alt">
                        <span id="newMessagesCount">
                            {{$userData->getNewMessages()}}
                        </span>
                    </a>
                    <span>
                        <div class="name_hover">
                                <ul>
                                    <li><a href="/account">Profile</a></li>
                                    @if($userData->user_type == 'employee')
                                        <li><a href="/my_jobs">My Jobs</a></li>
                                    @else
                                        <li><a href="/my_orders">My Orders</a></li>
                                        <li><a href="/my_transactions">Transactions</a></li>
                                    @endif
                                    @if($userData->role == 'admin')
                                        <li><a href="/admin">Admin Panel</a></li>
                                    @endif
                                    <li><a href="/logout">Log Out</a></li>
                                </ul>
                        </div>
                    </span>
            </div>

        </div>
        @endif

    </div>

</header>