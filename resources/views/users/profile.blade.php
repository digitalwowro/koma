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
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Enter Your Profile Details</h2>
                </header>

                <div class="main-box-body clearfix">
                    <div class="main-box-body clearfix">
                        {!! Form::model($user, ['route' => 'profile.update', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                        @include('users._form')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
