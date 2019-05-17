@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('users.index') }}"><span>Users</span></a></li>
                <li class="active"><span>Add User</span></li>
            </ol>

            <h1>User Management <small>Add User</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Account Details</h2>
                </header>

                <div class="main-box-body clearfix">
                    <div class="main-box-body clearfix">
                        {!! Form::open(['route' => 'users.store', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                        @include('users._form')

                        @include('users._save')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
