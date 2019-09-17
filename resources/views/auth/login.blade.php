<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Koma</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="{{ elixir('css/all.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Koma</b>ADMIN
    </div>

    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        {!! Form::open(['url' => '/auth/login', 'method' => 'POST']) !!}
            <div class="form-group has-feedback">
                <input type="email" name="email" class="form-control" placeholder="Email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>


            @if (count($errors) > 0)
            <div class="row">
                <div class="col-xs-12">
                    <p style="color: red;">
                        {{ $errors->first() }}
                    </p>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-xs-12">
                    <label style="font-weight: normal;">Keep me logged in for</label>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-8">
                    <div class="form group has-feedback">
                        <select name="session" class="form-control">
                            <option value="1h">1 hour</option>
                            <option value="1d">1 day</option>
                            <option value="3d">3 days</option>
                            <option value="7d">7 days</option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>

<script src="{{ elixir('js/all.js') }}"></script>

</body>
</html>
