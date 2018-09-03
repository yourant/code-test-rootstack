<ul class="nav" id="side-menu">
    <li class="nav-header border-bottom">
        <div class="dropdown profile-element">
            <img alt="image" class="" src="/img/white-logo.png" style="width: 100%;" height="28">
            {{--<img src="/img/white-isologo.png" height="38">--}}
        </div>
        <div class="logo-element">
            <img src="/img/white-isologo.png" height="38">
        </div>
    </li>
    @foreach ($primary_menu as $menu)
        <li class="{!! (isset($menu['active']) and $menu['active']) ? 'active' : ''  !!}">
            @if (!isset($menu['items']))
                <a href="{!! $menu['link'] !!}" {!!  isset($menu['target']) && $menu['target'] ? ' target="' . $menu['target']. '"' : ''  !!}>
                    @if (isset($menu['icon']))<i class="fa fa-{!! $menu['icon'] !!}"></i>@endif
                    <span class="nav-label">{!! $menu['title']  !!}</span>
                </a>
            @else
                <a href="{!! isset($menu['link']) ? $menu['link'] : '#' !!}">
                    <i class="fa fa-{!! $menu['icon'] !!}"></i>
                    <span class="nav-label">{!! $menu['title'] !!}</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    @foreach ($menu['items'] as $item)
                        <li>
                            <a href="{!!  $item['link'] !!}">
                                @if (isset($item['icon']))<i class="fa fa-{!! $item['icon'] !!} fa-fw"></i>@endif
                                {!! $item['title'] !!}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>