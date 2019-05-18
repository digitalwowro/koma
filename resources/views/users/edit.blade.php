@extends('layout')

@section('content')
    <section class="content-header">
        <h1>User Management <small>Edit User</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('users.index') }}"><span>Users</span></a></li>
            <li class="active"><span>Edit User</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Account Details</h3>
            </div>

            <div class="box-body">
                {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                @include('users._form')

                @include('users._save')
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
