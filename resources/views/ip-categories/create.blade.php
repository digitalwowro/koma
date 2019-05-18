@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Categories <small>Add New IP Category</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('ip-categories.index') }}"><span>IP Categories</span></a></li>
            <li class="active"><span>Add new</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Enter Category Details</h3>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'ip-categories.store', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                    @include('ip-categories._form')
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
