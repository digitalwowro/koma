@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><span>Devices</span></li>
                <li><a href="{{ route('devices.index', $deviceSection->id) }}"><span>{{ $deviceSection->title }}</span></a></li>
                <li class="active"><span>Add new</span></li>
            </ol>

            <h1>Devices<small>Add New {{ str_singular($deviceSection->title) }} Device</small></h1>
        </div>
    </div>

    {!! Form::open(['route' => ['devices.store', $deviceSection->id], 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Enter {{ str_singular($deviceSection->title) }} Device Details</h2>
                </header>

                <div class="main-box-body clearfix device-form-fields">
                    @include('devices._form')
                </div>
            </div>
        </div>
    </div>

    @include('devices._ip-addresses')
    @include('devices._category')
    @include('devices._save')

    {!! Form::close() !!}

    @include('devices._modals')
@stop
