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
				$categories=array('one', 'two');
				$categories=array(
				'所有类别',
				'设计与创新',
				'写作与翻译',
				'其它选项',
				'网络设计与移动开发',
				'市场与销售',
				'法律与金融');
				
                $i = 1;
                $itemInCol = ceil(count($categories) / 4);

                ?>
                @foreach($categories as $category)

                    @if($category  == 'All Categories')
                        <a href="/jobs">All Categories</a>
                    @else
                        <a href="/jobs/category/{{$category}}">{{$category}}</a>
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
                        <a href="register"><button class="sign__up">注册</button></a>
                        <a href="/login"><button class="log__in">登陆</button></a>

                    @else
                        <span class="header_welcome__btn"><span class="header_welcome_txt">Profile</span>
                        <div class="user_btns_wrapper">
                        @if(Auth::user()->role == 'admin')
                            <a href="/account"><button>My Account</button></a>
                            <a href="/my_orders"><button>My Orders</button></a>
                            <a href="/my_transactions"><button>Transactions</button></a>
                            <a href="/admin/users"><button>Admin Panel</button></a>
                        @elseif(Auth::user()->user_type == 'employee')
                            <a href="/account"><button>My Account</button></a>
                            <a href="/my_jobs"><button>My Jobs</button></a>
                        @else
                            <a href="/account"><button>My Account</button></a>
                            <a href="/my_orders"><button>My Orders</button></a>
                            <a href="/my_transactions"><button>Transactions</button></a>
                        @endif
                            <a href="/logout"><button>Log Out</button></a>
                        </div>
                        </span>
                    @endif

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="main_header__title">

                    <h1>用最合算的价钱来雇佣各个行业领袖</h1>

                    <p>用我们的特殊的团购的方法与其它人共同分担费用</p>

                </div>

                @if (Auth::guest())

                    <div class="main_header__buttons">

                        {{--<a href="/register"><button class="email">Sign up with <span>email</span></button></a>--}}

                        <a href="/social_login/twitter"><button class="email"><i class="fa fa-twitter" aria-hidden="true"></i>用 <span>Twitter</span> 注册</button></a>

                        <a href="/social_login/facebook"><button class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i>用 <span>Facebook</span> 注册</button></a>

                    </div>

                @endif

                <div class="main_header__how">

                    <h2>使用方法</h2>

                    <div class="buttons">

                        <a class="popup-with-move-anim" href="#small-dialog2"><button class="startup">对于团购者</button></a>

                        <a class="popup-with-move-anim" href="#small-dialog"><button>对于招聘人才</button></a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</header>

