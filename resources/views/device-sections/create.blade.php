@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('device-sections.index') }}"><span>Device Sections</span></a></li>
                <li class="active"><span>Add new</span></li>
            </ol>

            <h1>Device Sections <small>Add New Device Section</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Enter Section Details</h2>
                </header>

                <div class="main-box-body clearfix">
                    <div class="main-box-body clearfix">
                        {!! Form::open(['route' => 'device-sections.store', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                            @include('device-sections._form')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
