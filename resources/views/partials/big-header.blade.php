<header class="main_header">
    <div class="dark_header__line">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <button class="ham"><img src="{{ asset('img/Category/ham.png') }}" alt="alt"></button>
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
        </div>

    </div>

    <div class="container">

        <div class="row">

            <div class="col-md-2 col-sm-12 col-xs-12">

                <div class="main_header__logo">

                    <a href="/"><img src="img/logo.png" alt="alt"></a>

                </div>



            </div>

            <div class="col-md-10 col-sm-12 col-xs-12">

                <div class="main_header__btn">


                    @if (Auth::guest())
                        <a href="register"><button>Sign Up</button></a>
                        <a href="/login"><button>Log In</button></a>

                    @else
                        <span class="header_welcome__btn">Welcome
                        <div class="user_btns_wrapper">
                        @if(Auth::user()->role == 'admin')
                            <a href="/account"><button>Profile</button></a>
                            <a href="/my_orders"><button>My Orders</button></a>
                            <a href="/my_transactions"><button>Transactions</button></a>
                            <a href="/admin/users"><button>Admin Panel</button></a>
                        @elseif(Auth::user()->role == 'employee')
                            <a href="/account"><button>Profile</button></a>
                            <a href="/my_jobs"><button>My Jobs</button></a>
                        @else
                            <a href="/account"><button>Profile</button></a>
                            <a href="/my_orders"><button>My Orders</button></a>
                            <a href="/my_transactions"><button>Transactions</button></a>
                        @endif
                        </div>
                        </span>
                    @endif

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="main_header__title">

                    <h1>{!! $pageTexts[1] !!}</h1>

                    <p>{!! $pageTexts[2] !!}</p>

                </div>

                @if (Auth::guest())

                    <div class="main_header__buttons">

                        {{--<a href="/register"><button class="email">Sign up with <span>email</span></button></a>--}}

                        <a href="/social_login/twitter"><button class="email"><i class="fa fa-twitter" aria-hidden="true"></i>Sign up with <span>Twitter</span></button></a>

                        <a href="/social_login/facebook"><button class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i>Sign up with <span>Facebook</span></button></a>

                    </div>

                @endif

                <div class="main_header__how">

                    <h2>{!! $pageTexts[7] !!}</h2>

                    <div class="buttons">

                        <a class="popup-with-move-anim" href="#small-dialog2"><button class="startup">For Buyers</button></a>

                        <a class="popup-with-move-anim" href="#small-dialog"><button>For Talent</button></a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</header>

