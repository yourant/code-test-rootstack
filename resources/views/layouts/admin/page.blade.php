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

        @yield('inline_styles')

        <script src="{{ cached_asset('/assets/javascript/admin-top.js') }}"></script>

        @if (!app()->isLocal())
            <script type='text/javascript'>
                (function (d, t) {
                    var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
                    bh.type = 'text/javascript';
                    bh.src = 'https://www.bugherd.com/sidebarv2.js?apikey={!! env('BUGHERD_KEY') !!}';
                    s.parentNode.insertBefore(bh, s);
                })(document, 'script');
            </script>
        @endif
    </head>
    <body class="skin-3 mini-navbar">
        <div id="wrapper">
            <nav class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    @include('layouts.admin.sidebar')
                </div>
            </nav>

            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    @include('layouts.admin.nav')
                </div>
                @if (view()->hasSection('breadcrumbs'))
                <div class="row wrapper border-bottom white-bg page-heading">
                    <div class="col-lg-12">
                        <h2>@yield('page_title')</h2>
                        @yield('breadcrumbs')
                    </div>
                </div>
                @endif
                <div class="wrapper wrapper-content">
                    @include('shared.alerts')
                    @yield('content')
                </div>
                <div class="footer">
                    <div>
                        <strong>Copyright</strong> MailAmericas &copy; {{ date('Y') }}
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ cached_asset('/assets/javascript/admin-bottom.js') }}"></script>

        @yield('inline_scripts')
    </body>
</html>
