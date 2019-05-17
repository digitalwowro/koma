@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="active"><span>Profile</span></li>
            </ol>

            <h1>Your Profile <small>Details</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            {!! Form::model($user, ['route' => 'profile.update', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
            <div class="main-box">
                <header class="main-box-header">
                    <h2 class="pull-left">Account Details</h2>
                </header>

                <div class="main-box-body">
                    <div class="main-box-body">
                        @include('users._form')
                    </div>
                </div>
            </div>

            <div class="main-box">
                <header class="main-box-header">
                    <h2 class="pull-left">Personalization</h2>
                </header>

                <div class="main-box-body">
                    <div class="main-box-body">
                        @include('users._personalization')
                    </div>
                </div>
            </div>

            <div class="main-box">
                <div class="main-box-body" style="padding: 20px 20px 0 20px;">
                    <div class="main-box-body" style="padding: 0;">
                        @include('users._save')
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
