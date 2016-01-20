@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="active"><span>IP Fields</span></li>
            </ol>

            <h1>IP Fields <small>List All Fields</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Categories</h2>

                    <div class="filter-block pull-right">
                        <a href="{{ route('ip-fields.create') }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Add Field
                        </a>
                    </div>
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                            <tr>
                                <th style="width:1px;">#</th>
                                <th><span>Title</span></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ($fields as $field)
                                <tr>
                                    <td>
                                        <i class="fa fa-reorder"></i>
                                    </td>
                                    <td>
                                        {{ $field->title }}
                                    </td>
                                    <td style="width: 1%; white-space: nowrap;">
                                        <a href="{{ route('ip-fields.edit', $field->id) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::open(['route' => ['ip-fields.destroy', $field->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                        <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this category?')) $(this).closest('form').submit();">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align:center;">
                                            There are currently no fields added. How about <a href="{{ route('ip-fields.create') }}">creating one</a> now?
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
