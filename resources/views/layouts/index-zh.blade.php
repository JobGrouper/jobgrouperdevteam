<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>@yield('title')</title>

    <script type="text/javascript">
        var jg_domain = "{{ Request::root() }}";
    </script>

    <link rel="icon" type="image/ico" href="{{ asset('img/jb_16.ico') }}" sizes="16x16">
    <link rel="icon" type="image/ico" href="{{ asset('img/jb_32.ico') }}" sizes="32x32">
    <link rel="icon" type="image/ico" href="{{ asset('img/jb_64.ico') }}" sizes="64x64">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,800italic,800,700italic,700,600italic,400italic,600,300,300italic' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="{{ asset('libs/bootstrap/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/font-awesome-4.2.0/css/font-awesome.min.css') }}">

	<link rel="stylesheet" href="{{ asset('libs/magnific/magnific-popup.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/Cropper/assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('libs/Cropper/dist/cropper.css') }}">

    <link rel="stylesheet" href="{{ asset('css/libs.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    <link rel="stylesheet" href="{{ asset('css/media.css') }}">

</head>

<body>
    <div class="dark_bg"></div>
	<div id="small-dialog" class="zoom-anim-dialog mfp-hide">
    <h1>对于招聘人才</h1>
    <p>与其选择很多的一次性工作，不知道下次什么时候能找到工作拿到工资，
	还不如加入JobGrouper。 
	JobGrouper会帮你将各种的短暂的兼职工作变成全职工作。</p>
	</div>

	<div id="small-dialog2" class="zoom-anim-dialog mfp-hide">
    <h1>对于团购者</h1>
    <p>无论是创业者需要寻找顶级的编程人员,需要其它创业团队, 
	还是一群街坊邻居需要寻找房屋管理员，
	在我们网站上团购服务是最便宜最简单的方法</p>
	</div>
@include('partials.big-header-zh')


@include('partials.maintenance-message')


@yield('content')





@include('partials.footer-zh')

</body>

</html>
