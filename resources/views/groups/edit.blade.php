@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Categories <small>Edit Group</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('groups.index') }}"><span>Groups</span></a></li>
            <li class="active"><span>Edit Group</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Enter Group Details</h3>
            </div>

            <div class="box-body">
                {!! Form::model($group, ['route' => ['groups.update', $group->id], 'method' => 'PUT', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                @include('groups._form')
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
