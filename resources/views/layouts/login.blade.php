<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{{ csrf_token() }}}">

    <title> @yield('page_title') | MailAmericas </title>

    <link rel="stylesheet" href="/assets/stylesheets/base.min.css" type="text/css">
    <link rel="stylesheet" href="/assets/stylesheets/theme.min.css" type="text/css">

    <script src="/assets/javascript/admin-top.js"></script>
    <style type="text/css">
        body {
            background-color: #4D4D4D;
            /*background-image: url('/img/watermark.png');*/
            color: #f5f5f5;
        }
        .loginscreen.middle-box {
            height: auto;
            width: 440px;
            padding: 20px 30px;
            margin-top: -300px;
            margin-left: -220px;
            /*background-image: url('/img/escheresque_ste.png');*/
        }
        @media(max-width: 320px) {
            .loginscreen.middle-box {
                width: 300px;
                padding: 10px 20px;
                margin-left: -150px;
            }
        }
        .input-group {
            color: #333;
        }
        body, .btn, .form-control { font-size: 14px; }
    </style>
</head>

<body class="compact">

<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        @yield('content')
    </div>
</div>

<!-- Mainly scripts -->
<script src="/assets/javascript/admin-bottom.js"></script>

</body>

</html>