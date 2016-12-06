@extends('layouts.index')



@section('title', 'JobGrouper - employment marketplace')



@section('content')

    <section class="hire">

        <div class="container">

            <div class="col-md-12">

                <h2>{!! $pageTexts[5] !!}</h2>

                <p>{{$pageTexts[6]}}</p>

            </div>

        </div>

    </section>



    <div class="container">

        <div class="row">

            <div class="col-md-12">

                {{--@if(count($hotJobs) > 0)--}}

                    {{--@foreach($hotJobs as $hotJob)--}}

                        {{--<div class="slider clearfix">--}}

                            {{--<div class="slider_more">--}}

                                {{--<div class="slider_more__text">--}}

                                    {{--<div class="block bordered">--}}

                                        {{--<span class="amount">&dollar;{{$hotJob->monthly_price}}</span>--}}

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

                                            <span class="amount">&dollar;{{number_format( $job->monthly_price, 2 ) }}</span>

                                            <span class="per">per month</span>

                                        </div>

                                        <div class="block orange">

                                            <span class="dole">{{$job->sales_count}}/{{$job->max_clients_count}}</span>

                                            <span class="purchased">Purchased</span>

                                        </div>

                                    </div>

                                    <a href="/job/{{$job->id}}">
                                        <button>More Details</button>
                                    </a>

                                </div>

                                <div class="slider_img"
                                     style="background-image: url('images/jobs/j_{{$job->id}}.png');">
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

                                    <h4>{{(strlen($job->title) < 40 ? $job->title : substr($job->title, 0, 40).'...')}}</h4>

                                    <p class="text">{{$job->description}}</p>

                                    <p class="count clearfix"><span class="left">${{number_format( $job->monthly_price, 2 )}}/month</span><span class="right">{{$job->sales_count}}/{{$job->max_clients_count}} buyers</span></p>

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
