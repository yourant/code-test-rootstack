<nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        <a class="hidden-xs" style="display: inline-table; float: left;line-height: 55px; margin-left: 10px;">
            <img class="" src="/img/white-text.png" height="30">
        </a>
        {!! Form::open(['route' => 'packages.search', 'class' => 'm-l hidden-xs navbar-form-custom', 'role' => 'search']) !!}
            <div class="form-group">
                <input type="text" placeholder="Search for a package..." class="form-control" name="tracking" id="top-search">
            </div>
        {!! Form::close() !!}
    </div>
    <ul class="nav navbar-top-links navbar-right pull-right">
        @if ($user_name)
        <li>
            <a href="{{ url('auth/logout') }}" class=""><i class="fa fa-power-off fa-fw"></i> <span class="hidden-xs"> {{ $user_name }} </span></a>
        </li>
        @endif
    </ul>
</nav>