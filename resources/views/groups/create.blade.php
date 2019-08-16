@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Categories <small>Add New Group</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('groups.index') }}"><span>Groups</span></a></li>
            <li class="active"><span>Add new</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Enter Group Details</h3>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'groups.store', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                    @include('groups._form')
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
