@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Categories <small>Edit IP Subnet</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('subnet.index', $category) }}"><span>IP Subnets</span></a></li>
            <li class="active"><span>Edit Item</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Enter IP Subnet Details</h3>
            </div>

            <div class="box-body">
                {!! Form::model($subnet, ['route' => ['subnet.update', $subnet->id], 'method' => 'PUT', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                @include('subnet._form')
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
