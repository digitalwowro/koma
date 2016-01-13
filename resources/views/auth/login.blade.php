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
</head>
<body id="login-page">
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div id="login-box">
                <div id="login-box-holder">
                    <div class="row">
                        <div class="col-xs-12">
                            <header id="login-header">
                                <div id="login-logo">
                                    <img src="{{ asset('img/logo.png') }}" alt=""/>
                                </div>
                            </header>
                            <div id="login-box-inner">
                                {!! Form::open(['url' => '/auth/login', 'method' => 'POST']) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input class="form-control" type="email" placeholder="Email address" name="email" required>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                        <input type="password" class="form-control" placeholder="Password" name="password" required>
                                    </div>
                                    <div id="remember-me-wrapper">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="checkbox-nice">
                                                    <input type="checkbox" id="remember-me" checked="checked" />
                                                    <label for="remember-me">
                                                        Remember me
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <button type="submit" class="btn btn-success col-xs-12">Login</button>
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div id="login-box-footer">
                    <div class="row">
                        <div class="col-xs-12">
                            Forgot your password?
                            <a href="registration.html">
                                Request a password reset
                            </a>
                        </div>
                    </div>
                </div>
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

</body>
</html>
