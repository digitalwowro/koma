@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Categories <small>List All Categories</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="active"><span>IP Categories</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Categories</h3>

                <div class="filter-block pull-right">
                    <a href="{{ route('ip-categories.create') }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> Add Category
                    </a>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th><span>Title</span></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse ($ipCategories as $ipCategory)
                            @can('list', $ipCategory)
                                <tr>
                                    <td>
                                        <a href="{{ route('ip.index', $ipCategory->id) }}">
                                            {{ $ipCategory->title }}
                                        </a>
                                    </td>
                                    <td style="width: 1%; white-space: nowrap;">
                                        @can('edit', $ipCategory)
                                        <a href="{{ route('ip-categories.edit', $ipCategory->id) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        @endcan

                                        @can('delete', $ipCategory)
                                        {!! Form::open(['route' => ['ip-categories.destroy', $ipCategory->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                        <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this category?')) $(this).closest('form').submit();">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::close() !!}
                                        @endcan
                                    </td>
                                </tr>
                            @endcan
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;">
                                    There are currently no categories added. How about <a href="{{ route('ip-categories.create') }}">creating one</a> now?
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {!! $ipCategories->render() !!}
            </div>
        </div>
    </section>
@stop
