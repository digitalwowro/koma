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
<div class="login-box recovery-success">
    <div class="login-logo">
        <b>Koma</b>ADMIN
    </div>

    <div class="login-box-body">
        <h1 class="text-center">Your account has been recovered</h1>

        @include('partials._recovery-string-alert', [
            'recoveryString' => $recoveryString,
            'center' => true,
        ])

        <div class="row">
            <div class="col-xs-12">
                <a href="{{ route('login') }}" class="btn btn-primary btn-block btn-flat">Back to Login Page</a>
            </div>
        </div>
    </div>
</div>

<script src="{{ elixir('js/all.js') }}"></script>

</body>
</html>
