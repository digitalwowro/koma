@extends('layout')

@section('content')
    <section class="content-header">
        <h1>User Management <small>Add User</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('users.index') }}"><span>Users</span></a></li>
            <li class="active"><span>Add User</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Account Details</h3>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'users.store', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                @include('users._form')

                @include('users._save')
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
