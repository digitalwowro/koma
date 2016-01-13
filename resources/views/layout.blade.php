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

            <!-- global styles -->
    {!! HTML::style('css/compiled/theme_styles.css') !!}

            <!-- notifications -->
    {!! HTML::style('css/libs/ns-default.css') !!}
    {!! HTML::style('css/libs/ns-style-bar.css') !!}
    {!! HTML::style('css/libs/ns-style-theme.css') !!}

            <!-- google font libraries -->
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>

    <!-- Favicon -->
    <link type="image/x-icon" href="{{ asset('favicon.png') }}" rel="shortcut icon"/>

    <!--[if lt IE 9]>
    {!! HTML::script('js/html5shiv.js') !!}
    {!! HTML::script('js/respond.min.js') !!}
    <![endif]-->

    @yield('head')
</head>
<body>
<div id="theme-wrapper">
    <header class="navbar" id="header-navbar">
        <div class="container">
            <a href="index.html" id="logo" class="navbar-brand">
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
                                <i class="fa fa-bell"></i>
                                <span class="count">8</span>
                            </a>
                            <ul class="dropdown-menu notifications-list">
                                <li class="pointer">
                                    <div class="pointer-inner">
                                        <div class="arrow"></div>
                                    </div>
                                </li>
                                <li class="item-header">You have 6 new notifications</li>
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-comment"></i>
                                        <span class="content">New comment on â€Awesome P...</span>
                                        <span class="time"><i class="fa fa-clock-o"></i>13 min.</span>
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-plus"></i>
                                        <span class="content">New user registration</span>
                                        <span class="time"><i class="fa fa-clock-o"></i>13 min.</span>
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-envelope"></i>
                                        <span class="content">New Message from George</span>
                                        <span class="time"><i class="fa fa-clock-o"></i>13 min.</span>
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span class="content">New purchase</span>
                                        <span class="time"><i class="fa fa-clock-o"></i>13 min.</span>
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-eye"></i>
                                        <span class="content">New order</span>
                                        <span class="time"><i class="fa fa-clock-o"></i>13 min.</span>
                                    </a>
                                </li>
                                <li class="item-footer">
                                    <a href="#">
                                        View all notifications
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown hidden-xs">
                            <a class="btn dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"></i>
                                <span class="count">16</span>
                            </a>
                            <ul class="dropdown-menu notifications-list messages-list">
                                <li class="pointer">
                                    <div class="pointer-inner">
                                        <div class="arrow"></div>
                                    </div>
                                </li>
                                <li class="item first-item">
                                    <a href="#">
                                        <img src="{{ asset('img/samples/messages-photo-1.png') }}" alt=""/>
                                        <span class="content">
                                            <span class="content-headline">
                                                George Clooney
                                            </span>
                                            <span class="content-text">
                                                Look, just because I don't be givin' no man a foot massage don't make it
                                                right for Marsellus to throw...
                                            </span>
                                        </span>
                                        <span class="time"><i class="fa fa-clock-o"></i>13 min.</span>
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <img src="{{ asset('img/samples/messages-photo-2.png') }}" alt=""/>
                                        <span class="content">
                                            <span class="content-headline">
                                                Emma Watson
                                            </span>
                                            <span class="content-text">
                                                Look, just because I don't be givin' no man a foot massage don't make it
                                                right for Marsellus to throw...
                                            </span>
                                        </span>
                                        <span class="time"><i class="fa fa-clock-o"></i>13 min.</span>
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <img src="{{ asset('img/samples/messages-photo-3.png') }}" alt=""/>
                                        <span class="content">
                                            <span class="content-headline">
                                                Robert Downey Jr.
                                            </span>
                                            <span class="content-text">
                                                Look, just because I don't be givin' no man a foot massage don't make it
                                                right for Marsellus to throw...
                                            </span>
                                        </span>
                                        <span class="time"><i class="fa fa-clock-o"></i>13 min.</span>
                                    </a>
                                </li>
                                <li class="item-footer">
                                    <a href="#">
                                        View all messages
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown hidden-xs">
                            <a class="btn dropdown-toggle" data-toggle="dropdown">
                                New Item
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-archive"></i>
                                        New Product
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-shopping-cart"></i>
                                        New Order
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-sitemap"></i>
                                        New Category
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        <i class="fa fa-file-text"></i>
                                        New Page
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown hidden-xs">
                            <a class="btn dropdown-toggle" data-toggle="dropdown">
                                English
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="item">
                                    <a href="#">
                                        Spanish
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        German
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="#">
                                        Italian
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="nav-no-collapse pull-right" id="header-nav">
                    <ul class="nav navbar-nav pull-right">
                        <li class="mobile-search">
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

                        </li>
                        <li class="dropdown profile-dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ gravatar(auth()->user()->email, 159) }}" alt=""/>
                                <span class="hidden-xs">{{ auth()->user()->name }}</span> <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="user-profile.html"><i class="fa fa-user"></i>Profile</a></li>
                                <li><a href="#"><i class="fa fa-cog"></i>Settings</a></li>
                                <li><a href="#"><i class="fa fa-envelope-o"></i>Messages</a></li>
                                <li><a href="#"><i class="fa fa-power-off"></i>Logout</a></li>
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
                                            <li><a href="user-profile.html"><i class="fa fa-user"></i>Profile</a></li>
                                            <li><a href="#"><i class="fa fa-cog"></i>Settings</a></li>
                                            <li><a href="#"><i class="fa fa-envelope-o"></i>Messages</a></li>
                                            <li><a href="#"><i class="fa fa-power-off"></i>Logout</a></li>
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
                                    Resources Management
                                </li>
                                <li>
                                    <a href="index.html">
                                        <i class="fa fa-dashboard"></i>
                                        <span>Dashboard</span>
                                        <span class="label label-primary label-circle pull-right">28</span>
                                    </a>
                                </li>
                                <li {!! is_route('devices.*') !!}>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-server"></i>
                                        <span>Devices</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        @foreach ($deviceSections as $deviceSection)
                                        <li>
                                            <a href="{{ route('devices.index', $deviceSection->id) }}" class="active">
                                                {!! $deviceSection->present()->icon !!}
                                                {{ $deviceSection->title }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-ellipsis-h"></i>
                                        <span>IP Manager</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="email-inbox.html">
                                                Inbox
                                            </a>
                                        </li>
                                        <li>
                                            <a href="email-detail.html">
                                                Detail
                                            </a>
                                        </li>
                                        <li>
                                            <a href="email-compose.html">
                                                Compose
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-header hidden-sm hidden-xs">
                                    Category and Field Management
                                </li>
                                <li {!! is_route('device-sections.*') !!}>
                                    <a href="{{ route('device-sections.index') }}">
                                        <i class="fa fa-server"></i>
                                        <span>Device Sections</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('device-sections.index') }}">
                                        <i class="fa fa-ellipsis-h"></i>
                                        <span>IP Manager Sections</span>
                                    </a>
                                </li>
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

<!-- global scripts -->
{!! HTML::script('js/jquery.js') !!}
{!! HTML::script('js/bootstrap.js') !!}
{!! HTML::script('js/jquery.nanoscroller.min.js') !!}

        <!-- notifications -->
{!! HTML::script('js/modernizr.custom.js') !!}
{!! HTML::script('js/classie.js') !!}
{!! HTML::script('js/notificationFx.js') !!}

        <!-- theme scripts -->
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
