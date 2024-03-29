@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Your Profile</h1>
    </section>

    <section class="content">
        {!! Form::model($user, ['route' => 'profile.update', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}

        @if (session()->has('recovery_string'))
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-lock"></i>
                    One-Time Recovery String
                </h3>
            </div>

            @include('partials._recovery-string-alert', ['recoveryString' => session('recovery_string')])
        </div>
        @endif

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Account Details</h3>
            </div>

            <div class="box-body">
                @include('users._form', ['profile' => true])
            </div>
        </div>

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Personalization</h3>
            </div>

            <div class="box-body">
                @include('users._personalization')
            </div>

            <div class="box-footer">
                @include('users._save')
            </div>
        </div>

        {!! Form::close() !!}
    </section>
@stop
