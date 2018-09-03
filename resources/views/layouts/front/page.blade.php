<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="csrf-param" content="_token">

        <title> @yield('page_title') | MailAmericas </title>

        <link rel="stylesheet" href="{{ cached_asset("/assets/stylesheets/base.min.css") }}" type="text/css">
        <link rel="stylesheet" href="{{ cached_asset("/assets/stylesheets/theme.min.css") }}" type="text/css">
        <link rel="stylesheet" href="{{ cached_asset("/assets/stylesheets/application.min.css") }}" type="text/css">
        <link rel="stylesheet" href="{{ cached_asset("/assets/stylesheets/front.min.css") }}" type="text/css">

        <script src="{{ cached_asset('/assets/javascript/front-top.min.js') }}"></script>
    </head>
    <body class="skin-3 top-navigation">
        <div id="wrapper">
            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    @include('layouts.front.nav')
                </div>
                @if (view()->hasSection('breadcrumbs'))
                <div class="row wrapper border-bottom white-bg page-heading">
                    <div class="col-lg-12">
                        <h2>@yield('page_title')</h2>
                        @yield('breadcrumbs')
                    </div>
                </div>
                @endif
                <div class="wrapper wrapper-content animated fadeInUp">
                    @yield('content')
                </div>
                <div class="footer">
                    <strong>Copyright</strong> MailAmericas &copy; {{ date('Y') }}
                </div>
            </div>
        </div>

        <script src="{{ cached_asset('/assets/javascript/front-bottom.min.js') }}"></script>

        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-80883830-1', 'auto');
            ga('send', 'pageview');
        </script>

        @yield('inline_scripts')
    </body>
</html>
