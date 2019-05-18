@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Fields <small>List All Fields</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="active"><span>IP Fields</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Fields</h3>

                <div class="filter-block pull-right">
                    <a href="{{ route('ip-fields.create') }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> Add Field
                    </a>
                </div>
            </div>

            <div class="box-body">
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
    </section>
@stop
