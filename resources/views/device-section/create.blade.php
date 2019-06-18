@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Device Sections <small>Add New Device Section</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('device-section.index') }}"><span>Device Sections</span></a></li>
            <li class="active"><span>Add new</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Enter Section Details</h3>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'device-section.store', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                    @include('device-section._form')
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
