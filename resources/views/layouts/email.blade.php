<head>
<style>
    p {
	margin:0 0 10px;
    }

    a.button {
        transition: all .3s ease;
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        -o-transition: all .3s ease;
        display: block;
        width: 150px;
        text-align: center;
        margin: 20px auto;
        padding: 15px 20px;
        color: #fff;
        background: #3e6372;
        text-decoration: none;
        border-radius: 5px;
    }

    body {
	font-family: "Open Sans",sans-serif;
	font-size: 16px;
	font-weight: 400;
    }

    table, th, td, tr {
	border: 1px solid black;
	border-collapse:collapse;
    }

</style>
</head>

<body>
    @yield('content')
</body>
