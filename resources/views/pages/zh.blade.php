@extends('layouts.index-zh')



@section('title', 'JobGrouper - employment marketplace')



@section('content')

    <section class="hire">

        <div class="container">

            <div class="col-md-12">

                <h2>(pageTexts5)</h2>

                <p>用我们的特殊的团购的方法与其它人共同分担费用</p>

            </div>

        </div>

    </section>

	<?php
		// - title
		// - description
		// - image_url
		// - max_clients_count
		// - pre_orders
		// - purchase_price
		
		$jobs = array();
		
		$job_stuff = array(
			array('title' => '无限制的修改文章',
				'description' => '对于一个月的费用，我们资深专业编辑将为您所有的文章进行修改校对。无论您需要在大学期间拿到4.0的满分，或者需要修改您的最喜欢的工作的cover letter，或者需要帮助您发表文章，我们致力于为您提供优质的满意的服务！对于学生，我们的专业作家，不但能随时修改您的作业，还能修改您的邮件',
				'image_url' => 'img/Demo/j_1.png',
				'sales_count' => 3,
				'max_clients_count' => 5,
				'pre_orders' => 1,
				'price' => '99.00'),
			array('title' => '基金申请文书的作家',
				'description' => '购买者将会拥有一个专门写基金申请文书的专家',
				'image_url' => 'img/Demo/j_2.png',
				'sales_count' => 0,
				'max_clients_count' => 5,
				'pre_orders' => 0,
				'price' => '519.00'),
			array('title' => '随传随到的杂物工',
				'description' => '对于每个月非常合算的价钱，您将拥有一个随传随到的杂物工来帮您处理任何事物。',
				'image_url' => 'img/Demo/j_3.png',
				'sales_count' => 14,
				'max_clients_count' => 75,
				'pre_orders' => 5,
				'price' => '38.00'),
			array('title' => '中国的社交媒体营销',
				'description' => '您将拥有一个专业的精通中英文双语营销者来营销推广您的生意',
				'image_url' => 'img/Demo/j_4.png',
				'sales_count' => 0,
				'max_clients_count' => 15,
				'pre_orders' => 12,
				'price' => '84.00'),
			array('title' => '学校代理',
				'description' => '您将拥有一个校园代理，在全球范围内为您营销推广生意',
				'image_url' => 'img/Demo/j_5.png',
				'sales_count' => 0,
				'max_clients_count' => 3,
				'pre_orders' => 0,
				'price' => '200.00')
			);
			
		foreach($job_stuff as $item) {
			
			$job = new StdClass();
			$job->id = 1;
			$job->title = $item['title'];
			$job->description = $item['description'];
			$job->image_url = $item['image_url'];
			$job->max_clients_count = $item['max_clients_count'];
			$job->pre_orders = $item['pre_orders'];
			$job->sales_count = $item['sales_count'];
			$job->price = $item['price'];
			
			array_push($jobs, $job);
		}
		
	?>

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                {{--@if(count($hotJobs) > 0)--}}

                    {{--@foreach($hotJobs as $hotJob)--}}

                        {{--<div class="slider clearfix">--}}

                            {{--<div class="slider_more">--}}

                                {{--<div class="slider_more__text">--}}

                                    {{--<div class="block bordered">--}}

                                        {{--<span class="amount">&dollar;{{$hotJob->price}}</span>--}}

                                        {{--<span class="per">per month</span>--}}

                                    {{--</div>--}}

                                    {{--<div class="block orange">--}}

                                        {{--<span class="dole">{{$hotJob->sales_count}}/{{$hotJob->max_clients_count}}</span>--}}

                                        {{--<span class="purchased">Purchased</span>--}}

                                    {{--</div>--}}

                                {{--</div>--}}

                                {{--<a href="/job/{{$hotJob->id}}"><button>More Details</button></a>--}}

                            {{--</div>--}}

                            {{--<div class="slider_img" style="background-image: url('images/jobs/j_{{$hotJob->id}}.png');">--}}

                                {{--<h3>{{$hotJob->title}}</h3>--}}

                                {{--<p>{{$hotJob->description}}</p>--}}

                                {{--<div class="socials">--}}

                                    {{--<ul>--}}

                                        {{--<li><a href="https://uk-ua.facebook.com/" target="_blank"><i class="fa fa-facebook"></i></a></li>--}}

                                        {{--<li><a href="https://twitter.com/" target="_blank"><i class="fa fa-twitter"></i></a></li>--}}

                                        {{--<li><a href="https://plus.google.com/" target="_blank"><i class="fa fa-google-plus"></i></a></li>--}}

                                    {{--</ul>--}}

                                {{--</div>--}}

                            {{--</div>--}}

                        {{--</div>--}}
                    {{--@endforeach--}}
                    {{----}}
                {{--@endif--}}

            </div>

        </div>

        <div class="row">

            <div class="items clearfix">

                @if(count($jobs) > 0)
                    <?php
                        $i = 0;
                    ?>
                    @foreach($jobs as $job)
                        @if($i == 0)
                            <div class="slider clearfix">

                                <div class="slider_more">

                                    <div class="slider_more__text">

                                        <div class="block bordered">

                                            <span class="amount">&dollar;{{$job->price}}</span>

                                            <span class="per">每个月</span>

                                        </div>

                                        <div class="block orange">

                                            <span class="dole">{{$job->sales_count}}/{{$job->max_clients_count}}</span>

                                            <span class="purchased">购买</span>

                                        </div>
                                        <p class="count clearfix"><span class="left"> {!! $job->pre_orders ? $job->pre_orders.' pre-order(s)' : '' !!} </span></p>

                                    </div>

                                    <a href="/job/{{$job->id}}">
                                        <button>More Details</button>
                                    </a>

                                </div>

                                <div class="slider_img"
                                     style="background-image: url('{{$job->image_url}}');">
                                     <a href="/job/{{$job->id}}" class="hot_a"></a>
                                         <h3>{{$job->title}}</h3>

                                    <p>{{$job->description}}</p>


                                    <div class="socials">

                                        <ul>

                                            <li><a href="https://www.facebook.com/sharer/sharer.php?u=https%3A//jobgrouper.com/job/{{$job->id}}" target="_blank"><i
                                                            class="fa fa-facebook"></i></a></li>

                                            <li><a href="https://twitter.com/home?status=https%3A//jobgrouper.com/job/{{$job->id}}" target="_blank"><i
                                                            class="fa fa-twitter"></i></a></li>

                                            <li><a href="https://plus.google.com/share?url=https%3A//jobgrouper.com/job/{{$job->id}}" target="_blank"><i
                                                            class="fa fa-google-plus"></i></a></li>

                                        </ul>

                                    </div>


                                </div>

                            </div>
                        @else
                            <div class="col-md-3 col-sm-6 col-xs-12">

                                <a href="/job/{{$job->id}}">

                                <div class="items_item">

                                    <div class="img_wrapper"><img src="{{ asset($job->image_url)}}" alt="alt"></div>

                                    <h4>{{$job->title}}</h4>

                                    <p class="text">{{$job->description}}</p>

                                    <p class="count clearfix"><span class="left">${{$job->price}}/month</span><span class="right">{{$job->sales_count}}/{{$job->max_clients_count}} 购买者</span></p>
                                    <p class="count clearfix"><span class="left"> {!! $job->pre_orders ? $job->pre_orders.' pre-order(s)' : '<br>' !!} </span></p>
                                </div>

                                </a>

                            </div>
                        @endif
                        <?php
                            ++$i;
                        ?>
                    @endforeach

                @else

                    No jobs cards

                @endif

            </div>

        </div>

    </div>

@stop
