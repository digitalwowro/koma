@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Categories <small>Edit Category</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('category.index') }}"><span>Categories</span></a></li>
            <li class="active"><span>Edit Item</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Enter Category Details</h3>
            </div>

            <div class="box-body">
                {!! Form::model($category, ['route' => ['category.update', $category->id], 'method' => 'PUT', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                @include('category._form')
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
