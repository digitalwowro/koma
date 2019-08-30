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
                    <a href="{{ route('ip-category.create') }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> Add Category
                    </a>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Owner</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse ($ipCategories as $ipCategory)
                            <tr>
                                <td>
                                    <a href="{{ route('subnet.index', $ipCategory->id) }}">
                                        {{ $ipCategory->title }}
                                    </a>
                                </td>

                                <td>
                                    @can('admin')
                                        <a href="{{ route('users.edit', $ipCategory->owner->id) }}">
                                            {{ $ipCategory->owner->name }}
                                        </a>
                                    @else
                                        {{ $ipCategory->owner->name }}
                                    @endcan
                                </td>

                                <td style="width: 1%; white-space: nowrap;">
                                    <a href="{{ route('ip-category.show', $ipCategory->id) }}" class="table-link" title="View">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>

                                    @can('share', $ipCategory)
                                    <a href="#" class="table-link share-item" title="Share" data-id="{{ $ipCategory->id }}" data-human-id="{{ $ipCategory->title }}">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-share-alt fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                    @endcan

                                    @can('owner', $ipCategory)
                                    <a href="{{ route('ip-category.edit', $ipCategory->id) }}" class="table-link">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                    @endcan

                                    @can('owner', $ipCategory)
                                    {!! Form::open(['route' => ['ip-category.destroy', $ipCategory->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                    <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this category?')) $(this).closest('form').submit(); return false;">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr class="norows">
                                <td colspan="3" style="text-align:center;">
                                    There are currently no categories added. How about <a href="{{ route('ip-category.create') }}">creating one</a> now?
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {!! $ipCategories->render() !!}
            </div>
        </div>
    </section>

    @include('partials._share-modal', [
        'resource_type' => 'IP category',
        'create_permissions' => true,
    ])
@stop
