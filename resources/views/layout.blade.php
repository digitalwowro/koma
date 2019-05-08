<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>HipoAdmin</title>

    <!-- bootstrap -->
    {!! HTML::style('css/bootstrap/bootstrap.min.css') !!}

    <!-- libraries -->
    {!! HTML::style('css/libs/font-awesome.css') !!}
    {!! HTML::style('css/libs/nanoscroller.css') !!}
    {!! HTML::style('css/libs/select2.css') !!}

    <!-- global styles -->
    {!! HTML::style('css/compiled/theme_styles.css') !!}

    <!-- notifications -->
    {!! HTML::style('css/libs/ns-default.css') !!}
    {!! HTML::style('css/libs/ns-style-bar.css') !!}
    {!! HTML::style('css/libs/ns-style-theme.css') !!}

    <!-- data tables -->
    {!! HTML::style('css/libs/dataTables.fixedHeader.css') !!}
    {!! HTML::style('css/libs/dataTables.tableTools.css') !!}

    <!-- google font libraries -->
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>

    <!-- Favicon -->
    <link type="image/x-icon" href="{{ asset('favicon.png') }}" rel="shortcut icon"/>

    <!--[if lt IE 9]>
    {!! HTML::script('js/html5shiv.js') !!}
    {!! HTML::script('js/respond.min.js') !!}
    <![endif]-->

    {!! HTML::style('css/libs/datepicker.css') !!}

    {!! HTML::style('css/custom.css') !!}

    @yield('head')
</head>
<body class="theme-whbl">
<div id="theme-wrapper">
    <header class="navbar" id="header-navbar">
        <div class="container">
            <a href="{{ route('home') }}" id="logo" class="navbar-brand">
                <img src="{{ asset('img/logo.png') }}" alt="" class="normal-logo logo-white"/>
                <img src="{{ asset('img/logo-black.png') }}" alt="" class="normal-logo logo-black"/>
                <img src="{{ asset('img/logo-small.png') }}" alt="" class="small-logo hidden-xs hidden-sm hidden"/>
            </a>

            <div class="clearfix">
                <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="fa fa-bars"></span>
                </button>

                <div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
                    <ul class="nav navbar-nav pull-left">
                        <li>
                            <a class="btn" id="make-small-nav">
                                <i class="fa fa-bars"></i>
                            </a>
                        </li>
                        <li class="dropdown hidden-xs">
                            <a class="btn dropdown-toggle" data-toggle="dropdown">
                                Create Item
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($deviceSections as $deviceSection)
                                    <li class="item">
                                        <a href="{{ route('devices.create', $deviceSection->id) }}">
                                            {!! $deviceSection->present()->icon !!}
                                            Create {{ str_singular($deviceSection->title) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="nav-no-collapse pull-right" id="header-nav">
                    <ul class="nav navbar-nav pull-right">
                        <!--<li class="mobile-search">
                            <a class="btn">
                                <i class="fa fa-search"></i>
                            </a>

                            <div class="drowdown-search">
                                <form role="search">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Search...">
                                        <i class="fa fa-search nav-search-icon"></i>
                                    </div>
                                </form>
                            </div>

                        </li>-->
                        <li class="dropdown profile-dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ gravatar(auth()->user()->email, 159) }}" alt=""/>
                                <span class="hidden-xs">{{ auth()->user()->name }}</span> <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="{{ route('profile') }}"><i class="fa fa-user"></i>Profile</a></li>
                                <li><a href="/auth/logout"><i class="fa fa-power-off"></i>Logout</a></li>
                            </ul>
                        </li>
                        <li class="hidden-xxs">
                            <a class="btn" href="/auth/logout">
                                <i class="fa fa-power-off"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div id="page-wrapper" class="container">
        <div class="row">
            <div id="nav-col">
                <section id="col-left" class="col-left-nano">
                    <div id="col-left-inner" class="col-left-nano-content">
                        <div id="user-left-box" class="clearfix hidden-sm hidden-xs dropdown profile2-dropdown">
                            <img src="{{ gravatar(auth()->user()->email, 159) }}" alt=""/>
                            <div class="user-box">
                                    <span class="name">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            {{ auth()->user()->name }}
                                            <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href="{{ route('profile') }}"><i class="fa fa-user"></i>Profile</a></li>
                                            <li><a href="/auth/logout"><i class="fa fa-power-off"></i>Logout</a></li>
                                        </ul>
                                    </span>
                                    <span class="status">
                                        <i class="fa fa-circle"></i> Online
                                    </span>
                            </div>
                        </div>
                        <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                            <ul class="nav nav-pills nav-stacked">
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    Resource Management
                                </li>
                                <li>
                                    <a href="{{ route('home') }}">
                                        <i class="fa fa-dashboard"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li {!! is_route('devices.*') !!}>
                                    <a href="#" class="dropdown-toggle dropdown-nocaret">
                                        <i class="fa fa-server"></i>
                                        <span>Devices</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        @foreach ($deviceSections as $deviceSection)
                                        @can('list-section', $deviceSection)
                                        <li class="{{ is_route_bool('devices.*', ['type' => $deviceSection->id]) ? 'open' : '' }}">
                                            <a
                                                href="{{ route('devices.index', $deviceSection->id) }}"
                                                style="padding-left:44px;"
                                                class="{!! is_route('devices.index', false, ['type' => $deviceSection->id]) !!} {{ count($deviceSection->categories) ? 'dropdown-toggle dropdown-nocaret' : '' }}"
                                            >
                                                {!! $deviceSection->present()->icon !!}
                                                &nbsp;
                                                {{ $deviceSection->title }}
                                                @if (count($deviceSection->categories))
                                                <i class="fa fa-angle-right drop-icon"></i>
                                                @endif
                                            </a>

                                            @if (count($deviceSection->categories))
                                            <ul class="submenu" style="{{ is_route_bool('devices.*', ['type' => $deviceSection->id]) ? 'display:block' : '' }}">
                                                @foreach ($deviceSection->categories as $categoryId => $categoryName)
                                                <li>
                                                    <a href="{{ route('devices.index', [$deviceSection->id, $categoryId]) }}">{{ $categoryName }}</a>
                                                </li>
                                                @endforeach
                                                <li>
                                                    <a href="{{ route('devices.index', $deviceSection->id) }}">show all</a>
                                                </li>
                                            </ul>
                                            @endif
                                        </li>
                                        @endcan
                                        @endforeach
                                    </ul>
                                </li>
                                @can('admin')
                                <li {!! is_route('ip.*') !!}>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-ellipsis-h"></i>
                                        <span>IP Addresses</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        @foreach ($ipCategories as $ipCategory)
                                            <li>
                                                <a href="{{ route('ip.index', $ipCategory->id) }}" {!! is_route('ip.index', true, ['category' => $ipCategory->id]) !!}>
                                                    {{ $ipCategory->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                @endcan

                                @can('admin')
                                <li class="nav-header hidden-sm hidden-xs">
                                    Administration
                                </li>

                                <li {!! is_route('device-sections.*') !!}>
                                    <a href="{{ route('device-sections.index') }}">
                                        <i class="fa fa-server"></i>
                                        <span>Device Sections</span>
                                    </a>
                                </li>

                                <li {!! is_route('ip-categories.*') !!}>
                                    <a href="{{ route('ip-categories.index') }}">
                                        <i class="fa fa-ellipsis-h"></i>
                                        <span>IP Categories</span>
                                    </a>
                                </li>

                                <li {!! is_route('ip-fields.*') !!}>
                                    <a href="{{ route('ip-fields.index') }}">
                                        <i class="fa fa-ellipsis-h"></i>
                                        <span>IP Fields</span>
                                    </a>
                                </li>

                                @can('superadmin')
                                <li {!! is_route('users.*') !!}>
                                    <a href="{{ route('users.index') }}">
                                        <i class="fa fa-users"></i>
                                        <span>User Management</span>
                                    </a>
                                </li>
                                @endcan
                                @endcan
                            </ul>
                        </div>
                    </div>
                </section>
                <div id="nav-col-submenu"></div>
            </div>
            <div id="content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        @yield('content')
                    </div>
                </div>

                <footer id="footer-bar" class="row">
                    <p id="footer-copyright" class="col-xs-12">
                        &copy; {{ date('Y') }} WebHipo.
                    </p>
                </footer>
            </div>
        </div>
    </div>
</div>

{{-- global scripts --}}
{!! HTML::script('js/jquery.js') !!}
{!! HTML::script('js/jquery-ui.custom.min.js') !!}
{!! HTML::script('js/bootstrap.min.js') !!}
{!! HTML::script('js/jquery.nanoscroller.min.js') !!}
{!! HTML::script('js/js.cookie.js') !!}

{{-- notifications --}}
{!! HTML::script('js/modernizr.custom.js') !!}
{!! HTML::script('js/classie.js') !!}
{!! HTML::script('js/notificationFx.js') !!}

{{-- theme scripts --}}
{!! HTML::script('js/bootstrap-datepicker.js') !!}
{!! HTML::script('js/select2.min.js') !!}

{{-- data tables --}}
{!! HTML::script('js/jquery.dataTables.js') !!}
{!! HTML::script('js/dataTables.fixedHeader.js') !!}
{!! HTML::script('js/jquery.dataTables.bootstrap.js') !!}

<script>var userProfile = {!! json_encode(auth()->user()->profile) !!};</script>

{{-- custom scripts --}}
{!! HTML::script('js/scripts.js') !!}

<script>
    @foreach (['success', 'notice', 'warning', 'error'] as $type)
        @if (Session::has($type) && is_string(Session::get($type)))
        var notification = new NotificationFx({
            message : '<span class="icon fa fa-bullhorn fa-2x"></span><p>{{ addslashes(Session::get($type)) }}</p>',
            layout : 'bar',
            effect : 'slidetop',
            type : '{{ $type }}' // notice, warning or error
        });

        // show the notification
        notification.show();
        @endif
     @endforeach

     @if (count($errors) > 0)
         var notification = new NotificationFx({
            message : '<span class="icon fa fa-bullhorn fa-2x"></span><p>{{ addslashes($errors->first()) }}</p>',
            layout : 'bar',
            effect : 'slidetop',
            type : 'error' // notice, warning or error
         });

        // show the notification
        notification.show();
    @endif
</script>

@yield('footer')

</body>
</html>
