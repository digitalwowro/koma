<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Koma</title>

    <link rel="stylesheet" href="{{ elixir('css/all.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <link type="image/x-icon" href="{{ asset('favicon.png') }}" rel="shortcut icon">

    @yield('head')
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <a href="{{ route('home') }}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>A</b>LT</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Admin</b>LTE</span>
        </a>

        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    {{--
                    <li class="dropdown">
                        <a class="btn dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-plus"></i>
                            New Device
                            <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach ($deviceSections as $deviceSection)
                                @can('create', $deviceSection)
                                    @if (auth()->user()->deviceSectionVisible($deviceSection->id))
                                        <li class="item">
                                            <a href="{{ route('device.create', $deviceSection->id) }}">
                                                {!! $deviceSection->present()->icon !!}
                                                Add {{ Str::singular($deviceSection->title) }}
                                            </a>
                                        </li>
                                    @endif
                                @endcan
                            @endforeach
                        </ul>
                    </li>
                    --}}

                    <li class="dropdown user user-menu">
                        <a href="{{ route('profile') }}">
                            <img class="user-image" src="{{ gravatar(auth()->user()->email, 159) }}" alt=""/>
                            <span class="hidden-xs">{{ auth()->user()->name }}</span>
                        </a>
                    </li>

                    <li>
                        {!! Form::open(['route' => 'logout', 'method' => 'POST', 'id' => 'logout-form']) !!}
                        {!! Form::close() !!}
                        <a href="/auth/logout" data-toggle="control-sidebar" onclick="$('#logout-form').submit(); return false;"><i class="fa fa-power-off"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ gravatar(auth()->user()->email, 159) }}" class="img-circle" alt="">
                </div>

                <div class="pull-left info">
                    <p>{{ auth()->user()->name }}</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>

            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">
                    Resource Management
                </li>
                <li>
                    <a href="{{ route('home') }}">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="treeview {{ is_route('device.*', false) }}">
                    <a href="#">
                        <i class="fa fa-server"></i>
                        <span>Devices</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @foreach ($deviceSections as $deviceSection)
                            @if (auth()->user()->deviceSectionVisible($deviceSection->id))
                                @php
                                    $isActive = !!is_route('device.index', false, ['type' => $deviceSection->id]);
                                @endphp

                                <li class="
                                    {{ count($deviceSection->categories) ? 'treeview' : '' }}
                                    {{ is_route_bool('device.*', ['type' => $deviceSection->id]) ? 'active menu-open' : '' }}">

                                    <a href="{{ route('device.index', $deviceSection->id) }}" class="{{ $isActive ? 'active' : '' }}">
                                        {!! $deviceSection->present()->icon !!}
                                        {{ $deviceSection->title }}

                                        @if (count($deviceSection->categories))
                                            <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                        @endif
                                    </a>

                                    @if (count($deviceSection->categories))
                                        <ul class="treeview-menu">
                                            @foreach ($deviceSection->categories as $categoryId => $categoryName)
                                            <li class="{{ $isActive && request()->route()->parameter('category') === $categoryId ? 'active' : '' }}">
                                                <a href="{{ route('device.index', [$deviceSection->id, $categoryId]) }}">
                                                    <i class="fa fa-circle-o"></i>
                                                    {{ $categoryName }}
                                                </a>
                                            </li>
                                            @endforeach

                                            <li class="{{ $isActive && !request()->route()->parameter('category') ? 'active' : '' }}">
                                                <a href="{{ route('device.index', $deviceSection->id) }}">
                                                    <i class="fa fa-circle"></i>
                                                    show all
                                                </a>
                                            </li>
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>

                @php
                    $isActive = !!is_route('ip.*');
                @endphp
                <li class="treeview{{ $isActive ? ' active' : '' }}">
                    <a href="#">
                        <i class="fa fa-ellipsis-h"></i>
                        <span>IP Addresses</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @foreach ($ipCategories as $ipCategory)
                            @if (auth()->user()->ipCategoryVisible($ipCategory->id))
                                <li class="{{ $isActive && request()->route()->parameter('category') == $ipCategory->id ? 'active' : '' }}">
                                    <a href="{{ route('ip.index', $ipCategory->id) }}" {!! is_route('ip.index', true, ['category' => $ipCategory->id]) !!}>
                                        {{ $ipCategory->title }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>

                <li class="header">
                    Administration
                </li>

                <li {!! is_route('device-section.*') !!}>
                    <a href="{{ route('device-section.index') }}">
                        <i class="fa fa-server"></i>
                        <span>Device Sections</span>
                    </a>
                </li>

                <li {!! is_route('ip-category.*') !!}>
                    <a href="{{ route('ip-category.index') }}">
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

                @can('admin')
                    <li class="treeview {!! is_route(['users.*', 'groups.*'], false) !!}">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>User Management</span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                        </a>
                        <ul class="treeview-menu">
                            <li {!! is_route('users.*') !!}>
                                <a href="{{ route('users.index') }}">
                                    <i class="fa fa-user"></i>
                                    <span>User Accounts</span>
                                </a>
                            </li>

                            <li {!! is_route('groups.*') !!}">
                                <a href="{{ route('groups.index') }}">
                                    <i class="fa fa-users"></i>
                                    <span>Groups</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
            </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        @yield('content')
    </div>

    <footer class="main-footer">
        &copy; {{ date('Y') }} DigitalWow.
    </footer>

    <script>var userProfile = {!! json_encode(auth()->user()->profile) !!};</script>

    <script src="{{ elixir('js/all.js') }}"></script>

    <script>
        @foreach (['success', 'notice', 'warning', 'error'] as $type)
            @if (Session::has($type) && is_string(Session::get($type)))
            $.growl.{{ $type === 'success' ? 'notice' : $type }}({
                message: '{{ addslashes(Session::get($type)) }}'
            });
            @endif
        @endforeach

        @if (count($errors))
            $.growl.error({!! json_encode(['message' => $errors->first()]) !!});
        @endif

        var xsrf_token = Cookies.get("XSRF-TOKEN");
        $.ajaxSetup({ headers: { 'X-XSRF-TOKEN': xsrf_token } });
    </script>

    @yield('footer')
</div>
</body>
</html>
