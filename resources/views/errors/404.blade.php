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
            background: url({{asset('img/error/404.jpg')}});
            -webkit-background-size: 100% 100%;
            background-size: 100% 100%;
            height: 100vh;
        }
        h2  {
            display: block;
            font-size: 250px;
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
            color: #a3bf00;
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
        <h2><span>4</span>04</h2>
        <p>Oops! The page you were looking for doesn't exist.</p>
        <span class="descr">You may have mistyped the address or the page may have moved.</span>
        <a href="/">back to home page</a>
    </div>
</div>
</body>
</html>