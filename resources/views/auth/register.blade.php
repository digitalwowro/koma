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
        <p class="login-box-msg">Register a new account</p>

        {!! Form::open(['route' => 'register.post', 'method' => 'POST']) !!}
        <div class="form-group has-feedback">
            <input type="text" name="name" class="form-control" placeholder="Name" required>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
            <input type="password" name="password" class="form-control" placeholder="New Password" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
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
                <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    <br>

    <div style="text-align: center; font-size: 16px;">
        <a href="{{ route('login') }}">Go Back</a>
    </div>
</div>

<script src="{{ elixir('js/all.js') }}"></script>

@include('partials._growl-notifications')

</body>
</html>
