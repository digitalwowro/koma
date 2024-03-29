@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Devices<small>Add New {{ Str::singular($deviceSection->title) }} Device</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>Devices</span></li>
            <li><a href="{{ route('device.index', $deviceSection->id) }}"><span>{{ $deviceSection->title }}</span></a></li>
            <li class="active"><span>Add new</span></li>
        </ol>
    </section>

    <section class="content">
        {!! Form::open(['route' => ['device.store', $deviceSection->id], 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Enter {{ Str::singular($deviceSection->title) }} Device Details</h3>
            </div>

            <div class="box-body">
                @include('device._form')
            </div>
        </div>

        @include('device._ip-addresses')
        @include('device._category')
        @include('device._save')

        {!! Form::close() !!}
    </section>

    @include('device._modals')
@stop
