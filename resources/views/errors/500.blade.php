<!DOCTYPE html>
<html>
<head>
    <title>404 Error, Page Not Found.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        .content {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            text-align: center;
            color: #767069;
            background: url({{asset('img/error/500.jpg')}});
            -webkit-background-size: 100% 100%;
            background-size: 100% 100%;
            height: 100vh;
        }
        h2  {
            display: block;
            font-size: 250px;
            color: #000000;
        }
        p {
            font-size: 25px;
            display: block;
            text-transform: uppercase;
        }
        .descr {
            font-size: 16px;
            font-weight: 300;
            display: block;
        }
        h2 span {
            color: rgba(0,0,0,.4);
            line-height: 1;
            margin: 0;
            display: inline-block;
        }

        a:hover {
            text-decoration: none;
            color: #fefefe;
        }

        a {
            display: inline-block;
            font-weight: 400;
            border: 5px solid rgba(255,255,255,0.9);
            background: rgb(78,146,236);
            width: 271px;
            text-transform: uppercase;
            text-decoration: none;
            line-height: 44px;
            color: #fefefe;
            font-size: 18px;
            border-radius: 50px;
            box-sizing: border-box;
            margin-top: 70px;
        }

        @media only screen and (max-width : 990px) {
            h2 {
                font-size: 70px;
            }
        }

    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <h2>5<span>0</span>0</h2>
        <span class="descr">Sorry... It's not you. It's us</span>
        <p>Internal server error</p>
        <a href="/">Contact Us</a>
    </div>
</div>
</body>
</html>