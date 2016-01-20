@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('ip-categories.index') }}"><span>IP Categories</span></a></li>
                <li class="active"><span>Add new</span></li>
            </ol>

            <h1>IP Categories <small>Add New IP Category</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Enter Category Details</h2>
                </header>

                <div class="main-box-body clearfix">
                    <div class="main-box-body clearfix">
                        {!! Form::open(['route' => 'ip-categories.store', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                            @include('ip-categories._form')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
