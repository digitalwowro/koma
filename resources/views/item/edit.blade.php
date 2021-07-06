@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Devices<small>Edit {{ Str::singular($category->title) }} Device</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>Devices</span></li>
            <li><a href="{{ route('item.index', $category->id) }}"><span>{{ $category->title }}</span></a></li>
            <li class="active"><span>Edit</span></li>
        </ol>
    </section>

    <section class="content">
        {!! Form::model($device, ['route' => ['item.update', $device->id], 'method' => 'PUT', 'class' => 'form-horizontal', 'role' => 'form']) !!}

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Enter {{ Str::singular($category->title) }} Device Details</h3>
            </div>

            <div class="box-body">
                @include('item._form')
            </div>
        </div>

        @include('item._ip-addresses')
        @include('item._category')
        @include('item._save')

        {!! Form::close() !!}
    </section>

    @include('item._modals')
@stop
