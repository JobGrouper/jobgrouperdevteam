@extends('layouts.main')

@section('title', $categoryTitle)
@section('content')
    <div class="categories_wrapper">
        <div class="container">
            <div class="row">
                <h2>{{$categoryTitle}}</h2>
                <div class="items clearfix">
                @if(count($jobs) > 0)
                    @foreach($jobs as $job)
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <a href="/job/{{$job->id}}">
                                <div class="items_item">
                                    <div class="img_wrapper"><img src="{{ asset($job->image_url)}}" alt="alt"></div>
                                    <h4>
                                        {{(strlen($job->title) < 40 ? $job->title : substr($job->title, 0, 40).'...')}}
                                    </h4>
                                    <p class="text">{{$job->description}}</p>
                                    <p class="count clearfix"><span class="left">${{$job->salary}}/month</span><span class="right">{{$job->sales_count}}/{{$job['max_clients_count']}} buyers</span></p>
                                </div>
                                </a>
                            </div>
                    @endforeach
                @else
                        <div class="no_job">No jobs cards</div>
                @endif
                </div>
            </div>
        </div>
    </div>

    <div class="pagination">
        <?php
        if(count($jobs) > 0){

        $iCurr = (empty($_GET['page']) ? 1 : intval($_GET['page']));

        /*всего страниц или конечная страница*/
        $iEnd = $jobs->lastPage();

        /*левый и правый лимиты*/
        $iLeft = 2;
        $iRight = 2;
        ?>
        @if($iCurr != 1)
            <img src="{{ asset('img/Category/left.png')}}" alt="alt">
            <span class="prev"><a href="{{$pageUrl}}?page=1">First</a></span>
        @endif
        <div class="pagination_items">
            <?php
            if($iCurr > $iLeft && $iCurr < ($iEnd-$iRight))
            {
                for($i = $iCurr-$iLeft; $i<=$iCurr+$iRight; $i++)
                {
                    ?>
                    <a class="<?=($i == $iCurr ? 'selected' : '')?>" href="{{$pageUrl}}?page={{$i}}">{{$i}}</a>
                    <?php
                }
            }
            elseif($iCurr <= $iLeft)
            {
                $iSlice = 1+$iLeft-$iCurr;
                $to = ((($iCurr+($iRight+$iSlice)) < $iEnd) ? ($iCurr+($iRight+$iSlice)) : $iEnd);
                for($i=1; $i <= $to; $i++)
                {
                    ?>
                    <a class="<?=($i == $iCurr ? 'selected' : '')?>" href="{{$pageUrl}}?page={{$i}}">{{$i}}</a>
                    <?php
                }
            }
            else
            {
                $iSlice = $iRight-($iEnd - $iCurr);
                $from = ((($iCurr-($iLeft+$iSlice)) >= 1) ? ($iCurr-($iLeft+$iSlice)) : 1);
                for($i = $from; $i<=$iEnd; $i++)
                {
                    ?>
                    <a class="<?=($i == $iCurr ? 'selected' : '')?>" href="{{$pageUrl}}?page={{$i}}">{{$i}}</a>
                    <?php
                }
            }
            ?>
        </div>
        @if($iCurr != $jobs->lastPage())
        <span class="next"><a href="{{$pageUrl}}?page={{$jobs->lastPage()}}">Last</a></span>
        <img src="{{ asset('img/Category/right.png')}}" alt="alt">
        @endif
        <?php
            }
        ?>
    </div>
@stop